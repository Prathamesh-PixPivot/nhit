# 🚀 NHIT Manual GCP Deployment (No Terraform)

Complete step-by-step guide to deploy NHIT application on GCP without Terraform.

## 📋 Prerequisites

1. **Google Cloud SDK** installed and authenticated
2. **Docker** installed
3. **GCP Project** with billing enabled

## 🎯 Step-by-Step Deployment

### Step 1: Set Up Environment
```bash
# Set your project ID
export PROJECT_ID="your-gcp-project-id"
export REGION="us-central1"
export APP_NAME="nhit"

# Set the active project
gcloud config set project $PROJECT_ID
```

### Step 2: Enable Required APIs
```bash
gcloud services enable \
    cloudbuild.googleapis.com \
    run.googleapis.com \
    sqladmin.googleapis.com \
    redis.googleapis.com \
    secretmanager.googleapis.com \
    containerregistry.googleapis.com
```

### Step 3: Build and Push Docker Image
```bash
# Build the Docker image
docker build -t gcr.io/$PROJECT_ID/$APP_NAME-app:latest .

# Configure Docker to use gcloud as credential helper
gcloud auth configure-docker

# Push the image to Container Registry
docker push gcr.io/$PROJECT_ID/$APP_NAME-app:latest
```

### Step 4: Create Cloud SQL Database
```bash
# Create Cloud SQL instance
gcloud sql instances create $APP_NAME-db-instance \
    --database-version=MYSQL_8_0 \
    --tier=db-f1-micro \
    --region=$REGION \
    --storage-type=SSD \
    --storage-size=20GB \
    --backup-start-time=03:00 \
    --enable-bin-log \
    --authorized-networks=0.0.0.0/0

# Create the database
gcloud sql databases create ${APP_NAME}_production \
    --instance=$APP_NAME-db-instance

# Generate secure password and create user
DB_PASSWORD=$(openssl rand -base64 16)
gcloud sql users create ${APP_NAME}_user \
    --instance=$APP_NAME-db-instance \
    --password=$DB_PASSWORD

echo "Database Password: $DB_PASSWORD"
echo "Save this password - you'll need it for secrets!"
```

### Step 5: Create Redis Instance
```bash
# Create Redis instance
gcloud redis instances create $APP_NAME-redis \
    --size=1 \
    --region=$REGION \
    --redis-version=redis_7_0 \
    --auth-enabled
```

### Step 6: Create and Store Secrets
```bash
# Generate application key
APP_KEY=$(openssl rand -base64 32)

# Get database and Redis connection details
DB_HOST=$(gcloud sql instances describe $APP_NAME-db-instance --format="value(ipAddresses[0].ipAddress)")
REDIS_HOST=$(gcloud redis instances describe $APP_NAME-redis --region=$REGION --format="value(host)")
REDIS_AUTH=$(gcloud redis instances describe $APP_NAME-redis --region=$REGION --format="value(authString)")

# Create secrets in Secret Manager
echo "base64:$(echo -n $APP_KEY | base64)" | gcloud secrets create app-key --data-file=-
echo "$DB_HOST" | gcloud secrets create db-host --data-file=-
echo "${APP_NAME}_production" | gcloud secrets create db-name --data-file=-
echo "${APP_NAME}_user" | gcloud secrets create db-username --data-file=-
echo "$DB_PASSWORD" | gcloud secrets create db-password --data-file=-
echo "$REDIS_HOST" | gcloud secrets create redis-host --data-file=-
echo "$REDIS_AUTH" | gcloud secrets create redis-password --data-file=-

echo "✅ All secrets created successfully!"
```

### Step 7: Deploy to Cloud Run
```bash
# Deploy the application
gcloud run deploy $APP_NAME-app \
    --image=gcr.io/$PROJECT_ID/$APP_NAME-app:latest \
    --region=$REGION \
    --platform=managed \
    --allow-unauthenticated \
    --memory=2Gi \
    --cpu=1 \
    --max-instances=10 \
    --min-instances=1 \
    --port=80 \
    --set-env-vars="APP_ENV=production,APP_DEBUG=false,DB_CONNECTION=mysql,DB_PORT=3306,REDIS_PORT=6379,CACHE_DRIVER=redis,SESSION_DRIVER=redis,QUEUE_CONNECTION=redis" \
    --update-secrets="APP_KEY=app-key:latest" \
    --update-secrets="DB_HOST=db-host:latest" \
    --update-secrets="DB_DATABASE=db-name:latest" \
    --update-secrets="DB_USERNAME=db-username:latest" \
    --update-secrets="DB_PASSWORD=db-password:latest" \
    --update-secrets="REDIS_HOST=redis-host:latest" \
    --update-secrets="REDIS_PASSWORD=redis-password:latest"
```

### Step 8: Get Deployment Information
```bash
# Get the service URL
SERVICE_URL=$(gcloud run services describe $APP_NAME-app --region=$REGION --format="value(status.url)")

echo "🎉 Deployment Complete!"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "📱 Application URL: $SERVICE_URL"
echo "🗄️  Database IP: $DB_HOST"
echo "🐳 Container Image: gcr.io/$PROJECT_ID/$APP_NAME-app:latest"
echo "📍 Region: $REGION"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
```

## 🗄️ Database Setup

### Import Your Database
```bash
# Connect to Cloud SQL instance
gcloud sql connect $APP_NAME-db-instance --user=root

# Or use mysql client directly
mysql -h $DB_HOST -u ${APP_NAME}_user -p${DB_PASSWORD} ${APP_NAME}_production

# Import your existing database
mysql -h $DB_HOST -u ${APP_NAME}_user -p${DB_PASSWORD} ${APP_NAME}_production < nhit.sql
```

### Run Laravel Migrations (Alternative)
```bash
# If you prefer to run migrations instead of importing
# You'll need to connect to your Cloud Run instance or use Cloud Shell

# Option 1: Use Cloud Shell
gcloud cloud-shell ssh --command="
# Clone your repository in Cloud Shell
git clone YOUR_REPOSITORY_URL
cd nhit
composer install --no-dev
php artisan migrate --force
php artisan db:seed --force
"
```

## 🌐 Custom Domain (Optional)

```bash
# Map your custom domain
gcloud run domain-mappings create \
    --service=$APP_NAME-app \
    --domain=yourdomain.com \
    --region=$REGION

# Follow the DNS instructions provided by Google Cloud
```

## 📊 Monitor Your Deployment

```bash
# View application logs
gcloud logs read "resource.type=cloud_run_revision AND resource.labels.service_name=$APP_NAME-app" --limit=50

# Check service status
gcloud run services describe $APP_NAME-app --region=$REGION

# Monitor database
gcloud sql instances describe $APP_NAME-db-instance

# Monitor Redis
gcloud redis instances describe $APP_NAME-redis --region=$REGION
```

## 🔧 Troubleshooting

### Common Issues

1. **Database Connection Failed**
   ```bash
   # Check if Cloud SQL instance is running
   gcloud sql instances list
   
   # Verify secrets are created
   gcloud secrets list
   ```

2. **Application Won't Start**
   ```bash
   # Check Cloud Run logs
   gcloud logs read "resource.type=cloud_run_revision" --limit=20
   
   # Verify environment variables
   gcloud run services describe $APP_NAME-app --region=$REGION
   ```

3. **Redis Connection Issues**
   ```bash
   # Check Redis instance status
   gcloud redis instances list --region=$REGION
   
   # Verify Redis auth string
   gcloud redis instances describe $APP_NAME-redis --region=$REGION
   ```

## 💰 Cost Optimization

- **Cloud Run**: Auto-scales to zero (pay per request)
- **Cloud SQL**: Use `db-f1-micro` for development
- **Redis**: Start with 1GB memory
- **Total estimated cost**: ~$37-87/month

## 🔄 Updates and Redeployment

```bash
# To update your application:
# 1. Build new image
docker build -t gcr.io/$PROJECT_ID/$APP_NAME-app:latest .
docker push gcr.io/$PROJECT_ID/$APP_NAME-app:latest

# 2. Deploy new version
gcloud run deploy $APP_NAME-app \
    --image=gcr.io/$PROJECT_ID/$APP_NAME-app:latest \
    --region=$REGION
```

## 🎉 Success!

Your NHIT application is now running on Google Cloud Platform without Terraform!

**Container Image URL**: `gcr.io/$PROJECT_ID/$APP_NAME-app:latest`

This image can be used for:
- Manual deployments
- Other cloud providers  
- Local development
- Backup and recovery
