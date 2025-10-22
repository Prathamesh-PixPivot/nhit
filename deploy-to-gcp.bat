@echo off
REM NHIT Application GCP Deployment Script for Windows
REM This batch file automates the deployment of NHIT application to Google Cloud Platform

setlocal enabledelayedexpansion

REM Configuration
set APP_NAME=nhit
set REGION=us-central1

REM Colors (using echo with special characters)
set "GREEN=[92m"
set "RED=[91m"
set "YELLOW=[93m"
set "BLUE=[94m"
set "NC=[0m"

echo.
echo %BLUE%╔═══════════════════════════════════════════════════════════╗%NC%
echo %BLUE%║                 NHIT GCP Deployment Script               ║%NC%
echo %BLUE%║                      Windows Batch                       ║%NC%
echo %BLUE%║                                                           ║%NC%
echo %BLUE%║  This script will deploy your NHIT application to GCP    ║%NC%
echo %BLUE%╚═══════════════════════════════════════════════════════════╝%NC%
echo.

REM Get user inputs
set /p PROJECT_ID="Please enter your GCP Project ID: "
set /p CUSTOM_DOMAIN="Enter custom domain (optional, press Enter to skip): "

if "%PROJECT_ID%"=="" (
    echo %RED%❌ Project ID is required!%NC%
    pause
    exit /b 1
)

echo.
echo %BLUE%==== Step 1: Checking Prerequisites ====%NC%

REM Check if gcloud is installed
gcloud version >nul 2>&1
if errorlevel 1 (
    echo %RED%❌ Google Cloud SDK is not installed!%NC%
    echo Please install it from: https://cloud.google.com/sdk/docs/install-windows
    pause
    exit /b 1
)
echo %GREEN%✅ Google Cloud SDK found%NC%

REM Check if docker is installed
docker --version >nul 2>&1
if errorlevel 1 (
    echo %RED%❌ Docker is not installed!%NC%
    echo Please install Docker Desktop from: https://docs.docker.com/desktop/install/windows-install/
    pause
    exit /b 1
)
echo %GREEN%✅ Docker found%NC%

echo.
echo %BLUE%==== Step 2: Setting up GCP Project ====%NC%

REM Set the project
echo Setting GCP project to: %PROJECT_ID%
gcloud config set project %PROJECT_ID%

REM Enable required APIs
echo %BLUE%Enabling required APIs...%NC%
gcloud services enable cloudbuild.googleapis.com
gcloud services enable run.googleapis.com
gcloud services enable sqladmin.googleapis.com
gcloud services enable redis.googleapis.com
gcloud services enable secretmanager.googleapis.com
gcloud services enable containerregistry.googleapis.com

echo %GREEN%✅ GCP Project setup completed%NC%

echo.
echo %BLUE%==== Step 3: Building and Pushing Docker Image ====%NC%

set IMAGE_NAME=gcr.io/%PROJECT_ID%/%APP_NAME%-app:latest

echo Building Docker image: %IMAGE_NAME%
docker build -t %IMAGE_NAME% .
if errorlevel 1 (
    echo %RED%❌ Docker build failed!%NC%
    pause
    exit /b 1
)

echo Configuring Docker authentication...
gcloud auth configure-docker

echo Pushing image to Container Registry...
docker push %IMAGE_NAME%
if errorlevel 1 (
    echo %RED%❌ Docker push failed!%NC%
    pause
    exit /b 1
)

echo %GREEN%✅ Docker image built and pushed successfully%NC%

echo.
echo %BLUE%==== Step 4: Creating Infrastructure ====%NC%

set DB_INSTANCE_NAME=%APP_NAME%-db-instance
set DATABASE_NAME=%APP_NAME%_production
set DB_USERNAME=%APP_NAME%_user
set REDIS_INSTANCE_NAME=%APP_NAME%-redis

REM Generate random password (simple version for batch)
set DB_PASSWORD=%RANDOM%%RANDOM%%RANDOM%

echo Creating Cloud SQL instance: %DB_INSTANCE_NAME%
gcloud sql instances create %DB_INSTANCE_NAME% ^
    --database-version=MYSQL_8_0 ^
    --tier=db-f1-micro ^
    --region=%REGION% ^
    --storage-type=SSD ^
    --storage-size=20GB ^
    --backup-start-time=03:00 ^
    --enable-bin-log ^
    --authorized-networks=0.0.0.0/0

echo Creating database: %DATABASE_NAME%
gcloud sql databases create %DATABASE_NAME% --instance=%DB_INSTANCE_NAME%

echo Creating database user: %DB_USERNAME%
gcloud sql users create %DB_USERNAME% --instance=%DB_INSTANCE_NAME% --password=%DB_PASSWORD%

echo Creating Redis instance: %REDIS_INSTANCE_NAME%
gcloud redis instances create %REDIS_INSTANCE_NAME% ^
    --size=1 ^
    --region=%REGION% ^
    --redis-version=redis_7_0

echo %BLUE%Getting connection details...%NC%

REM Get connection details (simplified for batch)
for /f "delims=" %%i in ('gcloud sql instances describe %DB_INSTANCE_NAME% --format="value(ipAddresses[0].ipAddress)"') do set DB_HOST=%%i
for /f "delims=" %%i in ('gcloud redis instances describe %REDIS_INSTANCE_NAME% --region=%REGION% --format="value(host)"') do set REDIS_HOST=%%i
for /f "delims=" %%i in ('gcloud redis instances describe %REDIS_INSTANCE_NAME% --region=%REGION% --format="value(authString)"') do set REDIS_AUTH=%%i

REM Generate app key (simplified)
set APP_KEY=base64:YourGeneratedAppKeyHere32Characters

echo %BLUE%Creating secrets in Secret Manager...%NC%

REM Create secrets
echo %APP_KEY% | gcloud secrets create app-key --data-file=-
echo %DB_HOST% | gcloud secrets create db-host --data-file=-
echo %DATABASE_NAME% | gcloud secrets create db-name --data-file=-
echo %DB_USERNAME% | gcloud secrets create db-username --data-file=-
echo %DB_PASSWORD% | gcloud secrets create db-password --data-file=-
echo %REDIS_HOST% | gcloud secrets create redis-host --data-file=-
echo %REDIS_AUTH% | gcloud secrets create redis-password --data-file=-

echo %GREEN%✅ Infrastructure created successfully%NC%

echo.
echo %BLUE%==== Step 5: Deploying Application to Cloud Run ====%NC%

set SERVICE_NAME=%APP_NAME%-app

echo Deploying to Cloud Run service: %SERVICE_NAME%
gcloud run deploy %SERVICE_NAME% ^
    --image=%IMAGE_NAME% ^
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

if errorlevel 1 (
    echo %RED%❌ Cloud Run deployment failed!%NC%
    pause
    exit /b 1
)

echo %GREEN%✅ Application deployed successfully%NC%

REM Set up custom domain if provided
if not "%CUSTOM_DOMAIN%"=="" (
    echo.
    echo %BLUE%==== Setting up Custom Domain ====%NC%
    gcloud run domain-mappings create ^
        --service=%SERVICE_NAME% ^
        --domain=%CUSTOM_DOMAIN% ^
        --region=%REGION%
    echo %GREEN%✅ Domain mapping created for %CUSTOM_DOMAIN%%NC%
    echo %YELLOW%⚠️  Please update your DNS records as instructed by Google Cloud%NC%
)

echo.
echo %BLUE%==== Deployment Information ====%NC%

REM Get service URL
for /f "delims=" %%i in ('gcloud run services describe %SERVICE_NAME% --region=%REGION% --format="value(status.url)"') do set SERVICE_URL=%%i

echo.
echo %GREEN%🎉 Deployment Summary:%NC%
echo ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
echo 📱 Application URL: %SERVICE_URL%
echo 🗄️  Database IP: %DB_HOST%
echo 🐳 Container Image: %IMAGE_NAME%
echo 📍 Region: %REGION%
echo 🔑 Database Password: %DB_PASSWORD%
echo ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
echo.
echo %YELLOW%📋 Next Steps:%NC%
echo 1. Import your database or run migrations
echo 2. Set up your custom domain (if needed)
echo 3. Configure SSL certificate
echo 4. Set up monitoring and logging
echo 5. Configure backup strategies
echo.
echo %BLUE%💡 To import your database:%NC%
echo gcloud sql connect %DB_INSTANCE_NAME% --user=root
echo Then import your nhit.sql file
echo.
echo %GREEN%🎉 NHIT application deployment completed!%NC%

echo.
echo Press any key to exit...
pause >nul
