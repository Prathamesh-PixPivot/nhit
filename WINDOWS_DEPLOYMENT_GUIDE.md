# 🚀 NHIT Windows GCP Deployment Guide

Complete guide to deploy your NHIT Laravel application on Google Cloud Platform from Windows without Terraform.

## 📋 Prerequisites for Windows

### Required Software
1. **Google Cloud SDK** - [Download for Windows](https://cloud.google.com/sdk/docs/install-windows)
2. **Docker Desktop** - [Download for Windows](https://docs.docker.com/desktop/install/windows-install/)
3. **PowerShell 5.1+** (included with Windows 10/11)

### GCP Account Setup
1. Create a GCP account and project
2. Enable billing for your project
3. Install and authenticate gcloud CLI:
   ```cmd
   gcloud auth login
   gcloud config set project YOUR_PROJECT_ID
   ```

## 🚀 Windows Deployment Options

### Option 1: PowerShell Script (Recommended)
```powershell
# Run in PowerShell as Administrator
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
.\deploy-to-gcp.ps1
```

### Option 2: Batch File
```cmd
# Run in Command Prompt as Administrator
deploy-to-gcp.bat
```

### Option 3: Manual Commands (Windows)
Follow the step-by-step commands below.

## 🔧 Manual Deployment Steps (Windows Commands)

### Step 1: Set Environment Variables
```cmd
set PROJECT_ID=your-gcp-project-id
set REGION=us-central1
set APP_NAME=nhit
```

### Step 2: Enable Required APIs
```cmd
gcloud services enable cloudbuild.googleapis.com
gcloud services enable run.googleapis.com
gcloud services enable sqladmin.googleapis.com
gcloud services enable redis.googleapis.com
gcloud services enable secretmanager.googleapis.com
gcloud services enable containerregistry.googleapis.com
```

### Step 3: Build and Push Docker Image
```cmd
REM Build the Docker image
docker build -t gcr.io/%PROJECT_ID%/%APP_NAME%-app:latest .

REM Configure Docker authentication
gcloud auth configure-docker

REM Push to Container Registry
docker push gcr.io/%PROJECT_ID%/%APP_NAME%-app:latest
```

### Step 4: Create Cloud SQL Database
```cmd
REM Create Cloud SQL instance
gcloud sql instances create %APP_NAME%-db-instance ^
    --database-version=MYSQL_8_0 ^
    --tier=db-f1-micro ^
    --region=%REGION% ^
    --storage-type=SSD ^
    --storage-size=20GB ^
    --backup-start-time=03:00 ^
    --enable-bin-log ^
    --authorized-networks=0.0.0.0/0

REM Create the database
gcloud sql databases create %APP_NAME%_production ^
    --instance=%APP_NAME%-db-instance

REM Create database user (replace YOUR_PASSWORD with a secure password)
gcloud sql users create %APP_NAME%_user ^
    --instance=%APP_NAME%-db-instance ^
    --password=YOUR_SECURE_PASSWORD
```

### Step 5: Create Redis Instance
```cmd
gcloud redis instances create %APP_NAME%-redis ^
    --size=1 ^
    --region=%REGION% ^
    --redis-version=redis_7_0
```

### Step 6: Get Connection Details
```cmd
REM Get database IP
gcloud sql instances describe %APP_NAME%-db-instance --format="value(ipAddresses[0].ipAddress)"

REM Get Redis host
gcloud redis instances describe %APP_NAME%-redis --region=%REGION% --format="value(host)"

REM Get Redis auth string
gcloud redis instances describe %APP_NAME%-redis --region=%REGION% --format="value(authString)"
```

### Step 7: Create Secrets
```cmd
REM Create secrets (replace values with actual connection details)
echo base64:YOUR_32_CHARACTER_APP_KEY | gcloud secrets create app-key --data-file=-
echo YOUR_DB_HOST_IP | gcloud secrets create db-host --data-file=-
echo %APP_NAME%_production | gcloud secrets create db-name --data-file=-
echo %APP_NAME%_user | gcloud secrets create db-username --data-file=-
echo YOUR_DB_PASSWORD | gcloud secrets create db-password --data-file=-
echo YOUR_REDIS_HOST | gcloud secrets create redis-host --data-file=-
echo YOUR_REDIS_AUTH | gcloud secrets create redis-password --data-file=-
```

### Step 8: Deploy to Cloud Run
```cmd
gcloud run deploy %APP_NAME%-app ^
    --image=gcr.io/%PROJECT_ID%/%APP_NAME%-app:latest ^
    --region=%REGION% ^
    --platform=managed ^
    --allow-unauthenticated ^
    --memory=2Gi ^
    --cpu=1 ^
    --max-instances=10 ^
    --min-instances=1 ^
    --port=80 ^
    --set-env-vars="APP_ENV=production,APP_DEBUG=false,DB_CONNECTION=mysql,DB_PORT=3306,REDIS_PORT=6379,CACHE_DRIVER=redis,SESSION_DRIVER=redis,QUEUE_CONNECTION=redis" ^
    --update-secrets="APP_KEY=app-key:latest" ^
    --update-secrets="DB_HOST=db-host:latest" ^
    --update-secrets="DB_DATABASE=db-name:latest" ^
    --update-secrets="DB_USERNAME=db-username:latest" ^
    --update-secrets="DB_PASSWORD=db-password:latest" ^
    --update-secrets="REDIS_HOST=redis-host:latest" ^
    --update-secrets="REDIS_PASSWORD=redis-password:latest"
```

## 🗄️ Database Import (Windows)

### Option 1: Using Cloud SQL Proxy
```cmd
REM Download and install Cloud SQL Proxy
curl -o cloud_sql_proxy.exe https://dl.google.com/cloudsql/cloud_sql_proxy.exe

REM Connect to your instance
cloud_sql_proxy.exe -instances=%PROJECT_ID%:%REGION%:%APP_NAME%-db-instance=tcp:3306

REM In another command prompt, import your database
mysql -h 127.0.0.1 -u %APP_NAME%_user -p %APP_NAME%_production < nhit.sql
```

### Option 2: Direct Connection
```cmd
REM Connect directly using the public IP
mysql -h YOUR_DB_PUBLIC_IP -u %APP_NAME%_user -p %APP_NAME%_production < nhit.sql
```

## 🌐 Custom Domain Setup (Windows)
```cmd
gcloud run domain-mappings create ^
    --service=%APP_NAME%-app ^
    --domain=yourdomain.com ^
    --region=%REGION%
```

## 📊 Monitoring Commands (Windows)
```cmd
REM View application logs
gcloud logs read "resource.type=cloud_run_revision AND resource.labels.service_name=%APP_NAME%-app" --limit=50

REM Check service status
gcloud run services describe %APP_NAME%-app --region=%REGION%

REM Get service URL
gcloud run services describe %APP_NAME%-app --region=%REGION% --format="value(status.url)"
```

## 🔧 Windows-Specific Troubleshooting

### PowerShell Execution Policy
If you get execution policy errors:
```powershell
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
```

### Docker Desktop Issues
1. Ensure Docker Desktop is running
2. Enable WSL 2 backend if available
3. Check Docker settings for resource allocation

### gcloud Authentication Issues
```cmd
REM Re-authenticate if needed
gcloud auth login
gcloud auth application-default login

REM Check current configuration
gcloud config list
```

### Windows Firewall
If you have connection issues:
1. Allow Docker Desktop through Windows Firewall
2. Allow Google Cloud SDK through Windows Firewall

## 🎯 Quick Start Commands (Copy & Paste)

Replace `your-gcp-project-id` with your actual project ID:

```cmd
set PROJECT_ID=your-gcp-project-id
set REGION=us-central1
set APP_NAME=nhit

gcloud config set project %PROJECT_ID%
gcloud services enable cloudbuild.googleapis.com run.googleapis.com sqladmin.googleapis.com redis.googleapis.com secretmanager.googleapis.com containerregistry.googleapis.com

docker build -t gcr.io/%PROJECT_ID%/%APP_NAME%-app:latest .
gcloud auth configure-docker
docker push gcr.io/%PROJECT_ID%/%APP_NAME%-app:latest
```

## 💡 Pro Tips for Windows Users

1. **Use PowerShell ISE** for better script editing
2. **Run as Administrator** to avoid permission issues
3. **Use Windows Terminal** for better command line experience
4. **Enable WSL 2** for better Docker performance
5. **Use Git Bash** as alternative to Command Prompt

## 🎉 Success Indicators

After successful deployment, you should see:
- ✅ Docker image pushed to `gcr.io/PROJECT_ID/nhit-app:latest`
- ✅ Cloud Run service running at provided URL
- ✅ Database accessible via Cloud SQL
- ✅ Redis instance running
- ✅ All secrets stored in Secret Manager

## 📞 Windows-Specific Support

### Common Windows Issues:
1. **Path issues**: Use full paths for files
2. **Permission errors**: Run as Administrator
3. **Line ending issues**: Use `git config core.autocrlf true`
4. **PowerShell version**: Ensure you're using PowerShell 5.1+

### Useful Windows Commands:
```cmd
REM Check PowerShell version
$PSVersionTable.PSVersion

REM Check Docker status
docker info

REM Check gcloud installation
gcloud info

REM List all gcloud configurations
gcloud config configurations list
```

Your NHIT application is now ready for deployment on Windows! 🚀
