# Google Cloud Platform Infrastructure for NHIT Application
terraform {
  required_version = ">= 1.0"
  required_providers {
    google = {
      source  = "hashicorp/google"
      version = "~> 4.0"
    }
  }
}

# Configure the Google Cloud Provider
provider "google" {
  project = var.project_id
  region  = var.region
  zone    = var.zone
}

# Variables
variable "project_id" {
  description = "The GCP project ID"
  type        = string
}

variable "region" {
  description = "The GCP region"
  type        = string
  default     = "us-central1"
}

variable "zone" {
  description = "The GCP zone"
  type        = string
  default     = "us-central1-a"
}

variable "app_name" {
  description = "Application name"
  type        = string
  default     = "nhit"
}

# Enable required APIs
resource "google_project_service" "required_apis" {
  for_each = toset([
    "cloudbuild.googleapis.com",
    "run.googleapis.com",
    "sqladmin.googleapis.com",
    "redis.googleapis.com",
    "secretmanager.googleapis.com",
    "containerregistry.googleapis.com"
  ])
  
  project = var.project_id
  service = each.value
  
  disable_dependent_services = true
}

# Cloud SQL MySQL Instance
resource "google_sql_database_instance" "nhit_db" {
  name             = "${var.app_name}-db-instance"
  database_version = "MYSQL_8_0"
  region           = var.region
  
  settings {
    tier                        = "db-f1-micro"
    activation_policy           = "ALWAYS"
    availability_type           = "ZONAL"
    disk_type                   = "PD_SSD"
    disk_size                   = 20
    disk_autoresize             = true
    disk_autoresize_limit       = 100
    
    backup_configuration {
      enabled                        = true
      start_time                     = "03:00"
      location                       = var.region
      binary_log_enabled             = true
      transaction_log_retention_days = 7
      backup_retention_settings {
        retained_backups = 30
      }
    }
    
    ip_configuration {
      ipv4_enabled    = true
      authorized_networks {
        name  = "all"
        value = "0.0.0.0/0"
      }
    }
    
    database_flags {
      name  = "slow_query_log"
      value = "off"
    }
  }
  
  deletion_protection = false
  
  depends_on = [google_project_service.required_apis]
}

# Database
resource "google_sql_database" "nhit_database" {
  name     = "${var.app_name}_production"
  instance = google_sql_database_instance.nhit_db.name
}

# Database User
resource "google_sql_user" "nhit_user" {
  name     = "${var.app_name}_user"
  instance = google_sql_database_instance.nhit_db.name
  password = random_password.db_password.result
}

# Redis Instance
resource "google_redis_instance" "nhit_redis" {
  name           = "${var.app_name}-redis"
  tier           = "BASIC"
  memory_size_gb = 1
  region         = var.region
  
  redis_version     = "REDIS_7_0"
  display_name      = "NHIT Redis Cache"
  
  auth_enabled = true
  
  depends_on = [google_project_service.required_apis]
}

# Generate random passwords
resource "random_password" "db_password" {
  length  = 16
  special = true
}

resource "random_password" "redis_password" {
  length  = 16
  special = true
}

resource "random_password" "app_key" {
  length  = 32
  special = false
}

# Secret Manager Secrets
resource "google_secret_manager_secret" "nhit_secrets" {
  for_each = toset([
    "app-key",
    "db-host",
    "db-name", 
    "db-username",
    "db-password",
    "redis-host",
    "redis-password"
  ])
  
  secret_id = each.value
  
  replication {
    automatic = true
  }
  
  depends_on = [google_project_service.required_apis]
}

# Secret versions
resource "google_secret_manager_secret_version" "app_key" {
  secret      = google_secret_manager_secret.nhit_secrets["app-key"].id
  secret_data = "base64:${base64encode(random_password.app_key.result)}"
}

resource "google_secret_manager_secret_version" "db_host" {
  secret      = google_secret_manager_secret.nhit_secrets["db-host"].id
  secret_data = google_sql_database_instance.nhit_db.public_ip_address
}

resource "google_secret_manager_secret_version" "db_name" {
  secret      = google_secret_manager_secret.nhit_secrets["db-name"].id
  secret_data = google_sql_database.nhit_database.name
}

resource "google_secret_manager_secret_version" "db_username" {
  secret      = google_secret_manager_secret.nhit_secrets["db-username"].id
  secret_data = google_sql_user.nhit_user.name
}

resource "google_secret_manager_secret_version" "db_password" {
  secret      = google_secret_manager_secret.nhit_secrets["db-password"].id
  secret_data = google_sql_user.nhit_user.password
}

resource "google_secret_manager_secret_version" "redis_host" {
  secret      = google_secret_manager_secret.nhit_secrets["redis-host"].id
  secret_data = google_redis_instance.nhit_redis.host
}

resource "google_secret_manager_secret_version" "redis_password" {
  secret      = google_secret_manager_secret.nhit_secrets["redis-password"].id
  secret_data = google_redis_instance.nhit_redis.auth_string
}

# Cloud Run Service
resource "google_cloud_run_service" "nhit_app" {
  name     = "${var.app_name}-app"
  location = var.region
  
  template {
    spec {
      containers {
        image = "gcr.io/${var.project_id}/${var.app_name}-app:latest"
        ports {
          container_port = 80
        }
        
        resources {
          limits = {
            cpu    = "1000m"
            memory = "2Gi"
          }
          requests = {
            cpu    = "500m"
            memory = "1Gi"
          }
        }
        
        env {
          name  = "APP_ENV"
          value = "production"
        }
        
        env {
          name  = "APP_DEBUG"
          value = "false"
        }
        
        env {
          name = "APP_KEY"
          value_from {
            secret_key_ref {
              name = google_secret_manager_secret.nhit_secrets["app-key"].secret_id
              key  = "latest"
            }
          }
        }
        
        env {
          name = "DB_HOST"
          value_from {
            secret_key_ref {
              name = google_secret_manager_secret.nhit_secrets["db-host"].secret_id
              key  = "latest"
            }
          }
        }
        
        env {
          name = "DB_DATABASE"
          value_from {
            secret_key_ref {
              name = google_secret_manager_secret.nhit_secrets["db-name"].secret_id
              key  = "latest"
            }
          }
        }
        
        env {
          name = "DB_USERNAME"
          value_from {
            secret_key_ref {
              name = google_secret_manager_secret.nhit_secrets["db-username"].secret_id
              key  = "latest"
            }
          }
        }
        
        env {
          name = "DB_PASSWORD"
          value_from {
            secret_key_ref {
              name = google_secret_manager_secret.nhit_secrets["db-password"].secret_id
              key  = "latest"
            }
          }
        }
        
        env {
          name = "REDIS_HOST"
          value_from {
            secret_key_ref {
              name = google_secret_manager_secret.nhit_secrets["redis-host"].secret_id
              key  = "latest"
            }
          }
        }
        
        env {
          name = "REDIS_PASSWORD"
          value_from {
            secret_key_ref {
              name = google_secret_manager_secret.nhit_secrets["redis-password"].secret_id
              key  = "latest"
            }
          }
        }
      }
      
      container_concurrency = 80
      timeout_seconds       = 300
    }
    
    metadata {
      annotations = {
        "autoscaling.knative.dev/maxScale" = "10"
        "autoscaling.knative.dev/minScale" = "1"
        "run.googleapis.com/cpu-throttling" = "false"
        "run.googleapis.com/execution-environment" = "gen2"
      }
    }
  }
  
  traffic {
    percent         = 100
    latest_revision = true
  }
  
  depends_on = [
    google_project_service.required_apis,
    google_sql_database_instance.nhit_db,
    google_redis_instance.nhit_redis
  ]
}

# IAM policy for Cloud Run (allow public access)
resource "google_cloud_run_service_iam_member" "public_access" {
  service  = google_cloud_run_service.nhit_app.name
  location = google_cloud_run_service.nhit_app.location
  role     = "roles/run.invoker"
  member   = "allUsers"
}

# Outputs
output "cloud_run_url" {
  description = "URL of the deployed Cloud Run service"
  value       = google_cloud_run_service.nhit_app.status[0].url
}

output "database_ip" {
  description = "Public IP address of the Cloud SQL instance"
  value       = google_sql_database_instance.nhit_db.public_ip_address
}

output "redis_host" {
  description = "Redis instance host"
  value       = google_redis_instance.nhit_redis.host
}

output "container_image_url" {
  description = "Container image URL for manual deployment"
  value       = "gcr.io/${var.project_id}/${var.app_name}-app:latest"
}
