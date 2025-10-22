# 🚀 NHIT Application GCP Deployment Guide

Complete guide to deploy your NHIT Laravel application on Google Cloud Platform with containerization.

## 📋 Prerequisites

### Required Tools
- **Google Cloud SDK** - [Install Guide](https://cloud.google.com/sdk/docs/install)
- **Docker** - [Install Guide](https://docs.docker.com/get-docker/)
- **Terraform** (Optional) - [Install Guide](https://learn.hashicorp.com/tutorials/terraform/install-cli)

### GCP Account Setup
1. Create a GCP account and project
2. Enable billing for your project
3. Install and authenticate gcloud CLI:
   ```bash
   gcloud auth login
   gcloud config set project YOUR_PROJECT_ID
   ```

## 🏗️ Architecture Overview

Your application will be deployed with:
- **Cloud Run** - Containerized application hosting
- **Cloud SQL** - MySQL database (managed)
- **Redis** - In-memory cache and session storage
- **Secret Manager** - Secure credential storage
- **Container Registry** - Docker image storage
- **Cloud Build** - Automated CI/CD pipeline

## 🚀 Quick Deployment (Automated)

### Option 1: One-Click Deployment Script
```bash
# Make the script executable
chmod +x deploy-to-gcp.sh

# Run the deployment script
./deploy-to-gcp.sh
```

The script will:
1. ✅ Check prerequisites
2. ✅ Set up GCP project and enable APIs
3. ✅ Build and push Docker image
4. ✅ Create infrastructure (database, Redis, secrets)
5. ✅ Deploy application to Cloud Run
6. ✅ Provide deployment information

## 🔧 Manual Deployment Steps

### Step 1: Enable Required APIs
```bash
gcloud services enable \
    cloudbuild.googleapis.com \
    run.googleapis.com \
    sqladmin.googleapis.com \
    redis.googleapis.com \
    secretmanager.googleapis.com \
    containerregistry.googleapis.com
```

### Step 2: Build and Push Docker Image
```bash
# Build the image
docker build -t gcr.io/YOUR_PROJECT_ID/nhit-app:latest .

# Configure Docker authentication
gcloud auth configure-docker

# Push to Container Registry
docker push gcr.io/YOUR_PROJECT_ID/nhit-app:latest
```

### Step 3: Create Infrastructure

#### Using Terraform (Recommended)
```bash
cd terraform

# Initialize Terraform
terraform init

# Copy and update variables
cp terraform.tfvars.example terraform.tfvars
# Edit terraform.tfvars with your project details

# Plan and apply
terraform plan
terraform apply
```

#### Manual Infrastructure Creation
```bash
# Create Cloud SQL instance
gcloud sql instances create nhit-db-instance \
    --database-version=MYSQL_8_0 \
    --tier=db-f1-micro \
    --region=us-central1 \
    --storage-type=SSD \
    --storage-size=20GB

# Create database
gcloud sql databases create nhit_production \
    --instance=nhit-db-instance

# Create database user
gcloud sql users create nhit_user \
    --instance=nhit-db-instance \
    --password=YOUR_SECURE_PASSWORD

# Create Redis instance
gcloud redis instances create nhit-redis \
    --size=1 \
    --region=us-central1 \
    --redis-version=redis_7_0
```

### Step 4: Store Secrets
```bash
# Create secrets in Secret Manager
echo "base64:YOUR_APP_KEY" | gcloud secrets create app-key --data-file=-
echo "DB_HOST_IP" | gcloud secrets create db-host --data-file=-
echo "nhit_production" | gcloud secrets create db-name --data-file=-
echo "nhit_user" | gcloud secrets create db-username --data-file=-
echo "DB_PASSWORD" | gcloud secrets create db-password --data-file=-
echo "REDIS_HOST" | gcloud secrets create redis-host --data-file=-
echo "REDIS_AUTH_STRING" | gcloud secrets create redis-password --data-file=-
```

### Step 5: Deploy to Cloud Run
```bash
gcloud run deploy nhit-app \
    --image=gcr.io/YOUR_PROJECT_ID/nhit-app:latest \
    --region=us-central1 \
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
```

## 🗄️ Database Migration

### Connect to Cloud SQL
```bash
# Get connection details
gcloud sql instances describe nhit-db-instance

# Connect using Cloud SQL Proxy
gcloud sql connect nhit-db-instance --user=root

# Or connect directly with IP
mysql -h DB_PUBLIC_IP -u nhit_user -p nhit_production
```

### Run Migrations
```bash
# Option 1: Connect to Cloud Run instance
gcloud run services proxy nhit-app --port=8080

# Option 2: Use Cloud Shell
gcloud cloud-shell ssh --command="
cd /tmp &&
git clone YOUR_REPO_URL &&
cd nhit &&
php artisan migrate --force
"
```

## 🌐 Custom Domain Setup

### Map Custom Domain
```bash
gcloud run domain-mappings create \
    --service=nhit-app \
    --domain=yourdomain.com \
    --region=us-central1
```

### Update DNS Records
Add the following DNS records as instructed by Google Cloud:
- **CNAME** record pointing to `ghs.googlehosted.com`
- **TXT** record for domain verification

## 📊 Monitoring & Logging

### Enable Monitoring
```bash
# Cloud Monitoring
gcloud services enable monitoring.googleapis.com

# Cloud Logging
gcloud services enable logging.googleapis.com
```

### View Logs
```bash
# Application logs
gcloud logs read "resource.type=cloud_run_revision AND resource.labels.service_name=nhit-app"

# Database logs
gcloud logs read "resource.type=gce_instance AND resource.labels.instance_id=nhit-db-instance"
```

## 💰 Cost Optimization

### Estimated Monthly Costs
- **Cloud Run**: $0-50 (depending on traffic)
- **Cloud SQL (db-f1-micro)**: ~$7
- **Redis (1GB)**: ~$30
- **Storage & Networking**: ~$5-10

### Cost Optimization Tips
1. Use **Cloud Run** auto-scaling (min instances = 0)
2. Enable **Cloud SQL** automatic storage increase
3. Use **Preemptible instances** for development
4. Set up **budget alerts**

## 🔒 Security Best Practices

### Network Security
- Use **VPC** for internal communication
- Enable **Cloud Armor** for DDoS protection
- Configure **IAM roles** with least privilege

### Application Security
- Store secrets in **Secret Manager**
- Enable **Cloud SQL SSL**
- Use **HTTPS** only (automatic with Cloud Run)
- Regular **security updates**

## 🔄 CI/CD Pipeline

### Cloud Build Trigger
```bash
# Create build trigger
gcloud builds triggers create github \
    --repo-name=YOUR_REPO \
    --repo-owner=YOUR_USERNAME \
    --branch-pattern="^main$" \
    --build-config=cloudbuild.yaml
```

### Automated Deployments
The `cloudbuild.yaml` file provides:
- Automated builds on git push
- Container image building and pushing
- Automatic deployment to Cloud Run
- Environment-specific configurations

## 🛠️ Troubleshooting

### Common Issues

#### 1. Build Failures
```bash
# Check build logs
gcloud builds log BUILD_ID

# Common fixes
- Ensure Dockerfile is correct
- Check file permissions
- Verify dependencies in composer.json
```

#### 2. Database Connection Issues
```bash
# Check Cloud SQL status
gcloud sql instances describe nhit-db-instance

# Test connection
gcloud sql connect nhit-db-instance --user=nhit_user
```

#### 3. Application Errors
```bash
# View application logs
gcloud logs read "resource.type=cloud_run_revision" --limit=50

# Check environment variables
gcloud run services describe nhit-app --region=us-central1
```

### Health Checks
Your application includes health check endpoint at `/health`:
```bash
curl https://YOUR_CLOUD_RUN_URL/health
```

## 📚 Additional Resources

- [Cloud Run Documentation](https://cloud.google.com/run/docs)
- [Cloud SQL Documentation](https://cloud.google.com/sql/docs)
- [Laravel Deployment Guide](https://laravel.com/docs/deployment)
- [Docker Best Practices](https://docs.docker.com/develop/dev-best-practices/)

## 🆘 Support

### Getting Help
1. **GCP Console**: Monitor resources and logs
2. **Stack Overflow**: Community support
3. **Google Cloud Support**: Professional support plans
4. **Documentation**: Comprehensive guides and tutorials

### Container Image URL
After successful deployment, your container image will be available at:
```
gcr.io/YOUR_PROJECT_ID/nhit-app:latest
```

This image can be used for:
- Manual deployments
- Other cloud providers
- Local development
- Backup and recovery

---

## 🎉 Deployment Complete!

Your NHIT application is now running on Google Cloud Platform with:
- ✅ **Scalable** Cloud Run hosting
- ✅ **Managed** MySQL database
- ✅ **High-performance** Redis cache
- ✅ **Secure** secret management
- ✅ **Automated** CI/CD pipeline
- ✅ **Production-ready** configuration

**Next Steps:**
1. Configure your custom domain
2. Set up monitoring and alerting
3. Implement backup strategies
4. Optimize performance and costs
5. Set up staging environment

Happy deploying! 🚀
