#!/bin/bash

# NHIT Application GCP Deployment Script
# This script automates the deployment of NHIT application to Google Cloud Platform

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
PROJECT_ID=""
REGION="us-central1"
APP_NAME="nhit"
DOMAIN=""

# Functions
print_step() {
    echo -e "${BLUE}==== $1 ====${NC}"
}

print_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
}

check_prerequisites() {
    print_step "Checking Prerequisites"
    
    # Check if gcloud is installed
    if ! command -v gcloud &> /dev/null; then
        print_error "Google Cloud SDK is not installed. Please install it first."
        exit 1
    fi
    
    # Check if docker is installed
    if ! command -v docker &> /dev/null; then
        print_error "Docker is not installed. Please install it first."
        exit 1
    fi
    
    # Check if terraform is installed
    if ! command -v terraform &> /dev/null; then
        print_warning "Terraform is not installed. Infrastructure will be created manually."
    fi
    
    print_success "Prerequisites check completed"
}

setup_gcp_project() {
    print_step "Setting up GCP Project"
    
    if [ -z "$PROJECT_ID" ]; then
        echo "Please enter your GCP Project ID:"
        read -r PROJECT_ID
    fi
    
    # Set the project
    gcloud config set project "$PROJECT_ID"
    
    # Enable required APIs
    print_step "Enabling required APIs"
    gcloud services enable \
        cloudbuild.googleapis.com \
        run.googleapis.com \
        sqladmin.googleapis.com \
        redis.googleapis.com \
        secretmanager.googleapis.com \
        containerregistry.googleapis.com
    
    print_success "GCP Project setup completed"
}

build_and_push_image() {
    print_step "Building and Pushing Docker Image"
    
    # Build the image
    docker build -t "gcr.io/$PROJECT_ID/$APP_NAME-app:latest" .
    
    # Configure Docker to use gcloud as a credential helper
    gcloud auth configure-docker
    
    # Push the image
    docker push "gcr.io/$PROJECT_ID/$APP_NAME-app:latest"
    
    print_success "Docker image built and pushed successfully"
}

create_infrastructure() {
    print_step "Creating Infrastructure"
    
    if command -v terraform &> /dev/null; then
        # Use Terraform
        cd terraform
        
        # Initialize Terraform
        terraform init
        
        # Create terraform.tfvars if it doesn't exist
        if [ ! -f terraform.tfvars ]; then
            cp terraform.tfvars.example terraform.tfvars
            sed -i "s/your-gcp-project-id/$PROJECT_ID/g" terraform.tfvars
            print_warning "Please review and update terraform/terraform.tfvars file"
            read -p "Press enter to continue after updating the file..."
        fi
        
        # Plan and apply
        terraform plan
        terraform apply -auto-approve
        
        cd ..
        print_success "Infrastructure created using Terraform"
    else
        # Manual creation
        print_warning "Creating infrastructure manually (Terraform not available)"
        
        # Create Cloud SQL instance
        print_step "Creating Cloud SQL instance"
        gcloud sql instances create "$APP_NAME-db-instance" \
            --database-version=MYSQL_8_0 \
            --tier=db-f1-micro \
            --region="$REGION" \
            --storage-type=SSD \
            --storage-size=20GB \
            --backup-start-time=03:00 \
            --enable-bin-log \
            --authorized-networks=0.0.0.0/0
        
        # Create database
        gcloud sql databases create "${APP_NAME}_production" \
            --instance="$APP_NAME-db-instance"
        
        # Create database user
        DB_PASSWORD=$(openssl rand -base64 16)
        gcloud sql users create "${APP_NAME}_user" \
            --instance="$APP_NAME-db-instance" \
            --password="$DB_PASSWORD"
        
        # Create Redis instance
        print_step "Creating Redis instance"
        gcloud redis instances create "$APP_NAME-redis" \
            --size=1 \
            --region="$REGION" \
            --redis-version=redis_7_0
        
        # Create secrets
        print_step "Creating secrets"
        APP_KEY=$(openssl rand -base64 32)
        DB_HOST=$(gcloud sql instances describe "$APP_NAME-db-instance" --format="value(ipAddresses[0].ipAddress)")
        REDIS_HOST=$(gcloud redis instances describe "$APP_NAME-redis" --region="$REGION" --format="value(host)")
        REDIS_AUTH=$(gcloud redis instances describe "$APP_NAME-redis" --region="$REGION" --format="value(authString)")
        
        # Store secrets in Secret Manager
        echo "base64:$(echo -n "$APP_KEY" | base64)" | gcloud secrets create app-key --data-file=-
        echo "$DB_HOST" | gcloud secrets create db-host --data-file=-
        echo "${APP_NAME}_production" | gcloud secrets create db-name --data-file=-
        echo "${APP_NAME}_user" | gcloud secrets create db-username --data-file=-
        echo "$DB_PASSWORD" | gcloud secrets create db-password --data-file=-
        echo "$REDIS_HOST" | gcloud secrets create redis-host --data-file=-
        echo "$REDIS_AUTH" | gcloud secrets create redis-password --data-file=-
        
        print_success "Infrastructure created manually"
    fi
}

deploy_application() {
    print_step "Deploying Application to Cloud Run"
    
    # Deploy using Cloud Build
    if [ -f cloudbuild.yaml ]; then
        gcloud builds submit --config=cloudbuild.yaml .
    else
        # Manual deployment
        gcloud run deploy "$APP_NAME-app" \
            --image="gcr.io/$PROJECT_ID/$APP_NAME-app:latest" \
            --region="$REGION" \
            --platform=managed \
            --allow-unauthenticated \
            --memory=2Gi \
            --cpu=1 \
            --max-instances=10 \
            --min-instances=1 \
            --port=80 \
            --set-env-vars="APP_ENV=production,APP_DEBUG=false" \
            --update-secrets="APP_KEY=app-key:latest" \
            --update-secrets="DB_HOST=db-host:latest" \
            --update-secrets="DB_DATABASE=db-name:latest" \
            --update-secrets="DB_USERNAME=db-username:latest" \
            --update-secrets="DB_PASSWORD=db-password:latest" \
            --update-secrets="REDIS_HOST=redis-host:latest" \
            --update-secrets="REDIS_PASSWORD=redis-password:latest"
    fi
    
    print_success "Application deployed successfully"
}

run_migrations() {
    print_step "Running Database Migrations"
    
    # Get the Cloud Run service URL
    SERVICE_URL=$(gcloud run services describe "$APP_NAME-app" --region="$REGION" --format="value(status.url)")
    
    print_warning "Please run migrations manually by accessing your Cloud Run service:"
    print_warning "1. Connect to Cloud SQL instance"
    print_warning "2. Import your database schema"
    print_warning "3. Run: php artisan migrate --force"
    print_warning "Service URL: $SERVICE_URL"
}

setup_domain() {
    if [ -n "$DOMAIN" ]; then
        print_step "Setting up Custom Domain"
        
        gcloud run domain-mappings create \
            --service="$APP_NAME-app" \
            --domain="$DOMAIN" \
            --region="$REGION"
        
        print_success "Domain mapping created for $DOMAIN"
        print_warning "Please update your DNS records as instructed by Google Cloud"
    fi
}

show_deployment_info() {
    print_step "Deployment Information"
    
    SERVICE_URL=$(gcloud run services describe "$APP_NAME-app" --region="$REGION" --format="value(status.url)" 2>/dev/null || echo "Not deployed yet")
    DB_IP=$(gcloud sql instances describe "$APP_NAME-db-instance" --format="value(ipAddresses[0].ipAddress)" 2>/dev/null || echo "Not created yet")
    
    echo -e "${GREEN}🎉 Deployment Summary:${NC}"
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    echo "📱 Application URL: $SERVICE_URL"
    echo "🗄️  Database IP: $DB_IP"
    echo "🐳 Container Image: gcr.io/$PROJECT_ID/$APP_NAME-app:latest"
    echo "📍 Region: $REGION"
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    echo ""
    echo -e "${YELLOW}📋 Next Steps:${NC}"
    echo "1. Run database migrations"
    echo "2. Set up your custom domain (if needed)"
    echo "3. Configure SSL certificate"
    echo "4. Set up monitoring and logging"
    echo "5. Configure backup strategies"
}

# Main execution
main() {
    echo -e "${BLUE}"
    echo "╔═══════════════════════════════════════════════════════════╗"
    echo "║                 NHIT GCP Deployment Script               ║"
    echo "║                                                           ║"
    echo "║  This script will deploy your NHIT application to GCP    ║"
    echo "╚═══════════════════════════════════════════════════════════╝"
    echo -e "${NC}"
    
    # Get user inputs
    echo "Please provide the following information:"
    echo -n "GCP Project ID: "
    read -r PROJECT_ID
    echo -n "Custom Domain (optional, press enter to skip): "
    read -r DOMAIN
    
    # Execute deployment steps
    check_prerequisites
    setup_gcp_project
    build_and_push_image
    create_infrastructure
    deploy_application
    run_migrations
    setup_domain
    show_deployment_info
    
    print_success "🎉 NHIT application deployment completed!"
}

# Run the script
main "$@"
