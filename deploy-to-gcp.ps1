# NHIT Application GCP Deployment Script for Windows PowerShell
# This script automates the deployment of NHIT application to Google Cloud Platform

param(
    [string]$ProjectId = "",
    [string]$Region = "us-central1",
    [string]$AppName = "nhit",
    [string]$Domain = ""
)

# Colors for output
$Red = "Red"
$Green = "Green"
$Yellow = "Yellow"
$Blue = "Cyan"

function Write-Step {
    param([string]$Message)
    Write-Host "==== $Message ====" -ForegroundColor $Blue
}

function Write-Success {
    param([string]$Message)
    Write-Host "✅ $Message" -ForegroundColor $Green
}

function Write-Warning {
    param([string]$Message)
    Write-Host "⚠️  $Message" -ForegroundColor $Yellow
}

function Write-Error-Custom {
    param([string]$Message)
    Write-Host "❌ $Message" -ForegroundColor $Red
}

function Test-Prerequisites {
    Write-Step "Checking Prerequisites"
    
    # Check if gcloud is installed
    try {
        $null = Get-Command gcloud -ErrorAction Stop
        Write-Success "Google Cloud SDK found"
    }
    catch {
        Write-Error-Custom "Google Cloud SDK is not installed. Please install it first."
        Write-Host "Download from: https://cloud.google.com/sdk/docs/install-windows" -ForegroundColor Yellow
        exit 1
    }
    
    # Check if docker is installed
    try {
        $null = Get-Command docker -ErrorAction Stop
        Write-Success "Docker found"
    }
    catch {
        Write-Error-Custom "Docker is not installed. Please install Docker Desktop first."
        Write-Host "Download from: https://docs.docker.com/desktop/install/windows-install/" -ForegroundColor Yellow
        exit 1
    }
    
    Write-Success "Prerequisites check completed"
}

function Initialize-GcpProject {
    Write-Step "Setting up GCP Project"
    
    if (-not $ProjectId) {
        $ProjectId = Read-Host "Please enter your GCP Project ID"
    }
    
    # Set the project
    Write-Host "Setting GCP project to: $ProjectId" -ForegroundColor Blue
    gcloud config set project $ProjectId
    
    # Enable required APIs
    Write-Step "Enabling required APIs"
    $apis = @(
        "cloudbuild.googleapis.com",
        "run.googleapis.com", 
        "sqladmin.googleapis.com",
        "redis.googleapis.com",
        "secretmanager.googleapis.com",
        "containerregistry.googleapis.com"
    )
    
    foreach ($api in $apis) {
        Write-Host "Enabling $api..." -ForegroundColor Blue
        gcloud services enable $api
    }
    
    Write-Success "GCP Project setup completed"
    return $ProjectId
}

function Build-AndPushImage {
    param([string]$ProjectId)
    
    Write-Step "Building and Pushing Docker Image"
    
    $imageName = "gcr.io/$ProjectId/$AppName-app:latest"
    
    # Build the image
    Write-Host "Building Docker image: $imageName" -ForegroundColor Blue
    docker build -t $imageName .
    
    if ($LASTEXITCODE -ne 0) {
        Write-Error-Custom "Docker build failed"
        exit 1
    }
    
    # Configure Docker to use gcloud as credential helper
    Write-Host "Configuring Docker authentication..." -ForegroundColor Blue
    gcloud auth configure-docker
    
    # Push the image
    Write-Host "Pushing image to Container Registry..." -ForegroundColor Blue
    docker push $imageName
    
    if ($LASTEXITCODE -ne 0) {
        Write-Error-Custom "Docker push failed"
        exit 1
    }
    
    Write-Success "Docker image built and pushed successfully"
    return $imageName
}

function New-Infrastructure {
    param([string]$ProjectId)
    
    Write-Step "Creating Infrastructure"
    
    # Create Cloud SQL instance
    Write-Step "Creating Cloud SQL instance"
    $dbInstanceName = "$AppName-db-instance"
    
    Write-Host "Creating Cloud SQL instance: $dbInstanceName" -ForegroundColor Blue
    gcloud sql instances create $dbInstanceName `
        --database-version=MYSQL_8_0 `
        --tier=db-f1-micro `
        --region=$Region `
        --storage-type=SSD `
        --storage-size=20GB `
        --backup-start-time=03:00 `
        --enable-bin-log `
        --authorized-networks=0.0.0.0/0
    
    # Create database
    $databaseName = "${AppName}_production"
    Write-Host "Creating database: $databaseName" -ForegroundColor Blue
    gcloud sql databases create $databaseName --instance=$dbInstanceName
    
    # Create database user
    $dbUsername = "${AppName}_user"
    $dbPassword = -join ((1..16) | ForEach {[char]((65..90) + (97..122) + (48..57) | Get-Random)})
    
    Write-Host "Creating database user: $dbUsername" -ForegroundColor Blue
    gcloud sql users create $dbUsername --instance=$dbInstanceName --password=$dbPassword
    
    # Create Redis instance
    Write-Step "Creating Redis instance"
    $redisInstanceName = "$AppName-redis"
    
    Write-Host "Creating Redis instance: $redisInstanceName" -ForegroundColor Blue
    gcloud redis instances create $redisInstanceName `
        --size=1 `
        --region=$Region `
        --redis-version=redis_7_0
    
    # Generate app key
    $appKeyBytes = New-Object byte[] 32
    [Security.Cryptography.RNGCryptoServiceProvider]::Create().GetBytes($appKeyBytes)
    $appKey = [Convert]::ToBase64String($appKeyBytes)
    
    # Get connection details
    Write-Host "Getting connection details..." -ForegroundColor Blue
    $dbHost = gcloud sql instances describe $dbInstanceName --format="value(ipAddresses[0].ipAddress)"
    $redisHost = gcloud redis instances describe $redisInstanceName --region=$Region --format="value(host)"
    $redisAuth = gcloud redis instances describe $redisInstanceName --region=$Region --format="value(authString)"
    
    # Create secrets
    Write-Step "Creating secrets in Secret Manager"
    
    $secrets = @{
        "app-key" = "base64:$appKey"
        "db-host" = $dbHost
        "db-name" = $databaseName
        "db-username" = $dbUsername
        "db-password" = $dbPassword
        "redis-host" = $redisHost
        "redis-password" = $redisAuth
    }
    
    foreach ($secret in $secrets.GetEnumerator()) {
        Write-Host "Creating secret: $($secret.Key)" -ForegroundColor Blue
        $secret.Value | gcloud secrets create $secret.Key --data-file=-
    }
    
    Write-Success "Infrastructure created successfully"
    
    # Return connection info
    return @{
        DbHost = $dbHost
        DbPassword = $dbPassword
        RedisHost = $redisHost
    }
}

function Deploy-Application {
    param([string]$ProjectId, [string]$ImageName)
    
    Write-Step "Deploying Application to Cloud Run"
    
    $serviceName = "$AppName-app"
    
    Write-Host "Deploying to Cloud Run service: $serviceName" -ForegroundColor Blue
    
    gcloud run deploy $serviceName `
        --image=$ImageName `
        --region=$Region `
        --platform=managed `
        --allow-unauthenticated `
        --memory=2Gi `
        --cpu=1 `
        --max-instances=10 `
        --min-instances=1 `
        --port=80 `
        --set-env-vars="APP_ENV=production,APP_DEBUG=false,DB_CONNECTION=mysql,DB_PORT=3306,REDIS_PORT=6379,CACHE_DRIVER=redis,SESSION_DRIVER=redis,QUEUE_CONNECTION=redis" `
        --update-secrets="APP_KEY=app-key:latest" `
        --update-secrets="DB_HOST=db-host:latest" `
        --update-secrets="DB_DATABASE=db-name:latest" `
        --update-secrets="DB_USERNAME=db-username:latest" `
        --update-secrets="DB_PASSWORD=db-password:latest" `
        --update-secrets="REDIS_HOST=redis-host:latest" `
        --update-secrets="REDIS_PASSWORD=redis-password:latest"
    
    if ($LASTEXITCODE -ne 0) {
        Write-Error-Custom "Cloud Run deployment failed"
        exit 1
    }
    
    Write-Success "Application deployed successfully"
}

function Show-DeploymentInfo {
    param([string]$ProjectId, [hashtable]$ConnectionInfo)
    
    Write-Step "Deployment Information"
    
    try {
        $serviceUrl = gcloud run services describe "$AppName-app" --region=$Region --format="value(status.url)"
    }
    catch {
        $serviceUrl = "Not available"
    }
    
    Write-Host ""
    Write-Host "🎉 Deployment Summary:" -ForegroundColor Green
    Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Green
    Write-Host "📱 Application URL: $serviceUrl" -ForegroundColor White
    Write-Host "🗄️  Database IP: $($ConnectionInfo.DbHost)" -ForegroundColor White
    Write-Host "🐳 Container Image: gcr.io/$ProjectId/$AppName-app:latest" -ForegroundColor White
    Write-Host "📍 Region: $Region" -ForegroundColor White
    Write-Host "🔑 Database Password: $($ConnectionInfo.DbPassword)" -ForegroundColor Yellow
    Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Green
    Write-Host ""
    Write-Host "📋 Next Steps:" -ForegroundColor Yellow
    Write-Host "1. Import your database or run migrations"
    Write-Host "2. Set up your custom domain (if needed)"
    Write-Host "3. Configure SSL certificate"
    Write-Host "4. Set up monitoring and logging"
    Write-Host "5. Configure backup strategies"
    Write-Host ""
    Write-Host "💡 To import your database:" -ForegroundColor Blue
    Write-Host "gcloud sql connect $AppName-db-instance --user=root"
    Write-Host "Then import your nhit.sql file"
}

function Set-CustomDomain {
    param([string]$Domain)
    
    if ($Domain) {
        Write-Step "Setting up Custom Domain"
        
        gcloud run domain-mappings create `
            --service="$AppName-app" `
            --domain=$Domain `
            --region=$Region
        
        Write-Success "Domain mapping created for $Domain"
        Write-Warning "Please update your DNS records as instructed by Google Cloud"
    }
}

# Main execution
function Main {
    Write-Host ""
    Write-Host "╔═══════════════════════════════════════════════════════════╗" -ForegroundColor Blue
    Write-Host "║                 NHIT GCP Deployment Script               ║" -ForegroundColor Blue
    Write-Host "║                     Windows PowerShell                   ║" -ForegroundColor Blue
    Write-Host "║                                                           ║" -ForegroundColor Blue
    Write-Host "║  This script will deploy your NHIT application to GCP    ║" -ForegroundColor Blue
    Write-Host "╚═══════════════════════════════════════════════════════════╝" -ForegroundColor Blue
    Write-Host ""
    
    # Get user inputs if not provided
    if (-not $ProjectId) {
        $ProjectId = Read-Host "Please enter your GCP Project ID"
    }
    
    if (-not $Domain) {
        $Domain = Read-Host "Enter custom domain (optional, press Enter to skip)"
    }
    
    try {
        # Execute deployment steps
        Test-Prerequisites
        $ProjectId = Initialize-GcpProject
        $imageName = Build-AndPushImage -ProjectId $ProjectId
        $connectionInfo = New-Infrastructure -ProjectId $ProjectId
        Deploy-Application -ProjectId $ProjectId -ImageName $imageName
        Set-CustomDomain -Domain $Domain
        Show-DeploymentInfo -ProjectId $ProjectId -ConnectionInfo $connectionInfo
        
        Write-Success "🎉 NHIT application deployment completed!"
    }
    catch {
        Write-Error-Custom "Deployment failed: $($_.Exception.Message)"
        exit 1
    }
}

# Run the script
Main
