#!/bin/bash

# NHIT Production Deployment Script
# This script handles zero-downtime deployment of the NHIT Laravel application

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
DEPLOY_DIR="/opt/nhit"
BACKUP_DIR="/opt/nhit/backups"
LOG_FILE="/var/log/nhit-deploy.log"
COMPOSE_FILE="docker-compose.prod.yml"
ENV_FILE=".env.prod"

# Functions
log() {
    echo -e "${BLUE}[$(date +'%Y-%m-%d %H:%M:%S')]${NC} $1" | tee -a "$LOG_FILE"
}

error() {
    echo -e "${RED}[ERROR]${NC} $1" | tee -a "$LOG_FILE"
    exit 1
}

success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1" | tee -a "$LOG_FILE"
}

warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1" | tee -a "$LOG_FILE"
}

# Pre-deployment checks
pre_deployment_checks() {
    log "Starting pre-deployment checks..."
    
    # Check if Docker is running
    if ! docker info > /dev/null 2>&1; then
        error "Docker is not running"
    fi
    
    # Check if Docker Compose is available
    if ! command -v docker-compose > /dev/null 2>&1; then
        error "Docker Compose is not installed"
    fi
    
    # Check if required files exist
    if [[ ! -f "$COMPOSE_FILE" ]]; then
        error "Docker Compose file not found: $COMPOSE_FILE"
    fi
    
    if [[ ! -f "$ENV_FILE" ]]; then
        error "Environment file not found: $ENV_FILE"
    fi
    
    success "Pre-deployment checks passed"
}

# Create backup
create_backup() {
    log "Creating backup..."
    
    mkdir -p "$BACKUP_DIR"
    BACKUP_NAME="backup-$(date +%Y%m%d-%H%M%S)"
    BACKUP_PATH="$BACKUP_DIR/$BACKUP_NAME"
    
    # Backup database
    if docker-compose -f "$COMPOSE_FILE" --env-file "$ENV_FILE" ps db | grep -q "Up"; then
        log "Backing up database..."
        docker-compose -f "$COMPOSE_FILE" --env-file "$ENV_FILE" exec -T db mysqldump \
            -u root -p"${DB_ROOT_PASSWORD}" "${DB_DATABASE}" > "$BACKUP_PATH-database.sql"
        success "Database backup created: $BACKUP_PATH-database.sql"
    fi
    
    # Backup storage volumes
    log "Backing up storage volumes..."
    docker run --rm -v nhit_storage_data:/data -v "$BACKUP_DIR":/backup alpine \
        tar czf "/backup/$BACKUP_NAME-storage.tar.gz" -C /data .
    
    success "Backup created: $BACKUP_NAME"
}

# Pull latest images
pull_images() {
    log "Pulling latest Docker images..."
    
    # Load environment variables
    export $(grep -v '^#' "$ENV_FILE" | xargs)
    
    # Pull the application image
    docker pull "$DOCKER_IMAGE" || error "Failed to pull application image"
    
    # Pull other images
    docker-compose -f "$COMPOSE_FILE" --env-file "$ENV_FILE" pull
    
    success "Images pulled successfully"
}

# Run database migrations
run_migrations() {
    log "Running database migrations..."
    
    # Start database if not running
    docker-compose -f "$COMPOSE_FILE" --env-file "$ENV_FILE" up -d db redis
    
    # Wait for database to be ready
    log "Waiting for database to be ready..."
    sleep 30
    
    # Run migrations
    docker-compose -f "$COMPOSE_FILE" --env-file "$ENV_FILE" run --rm app \
        php artisan migrate --force || error "Migration failed"
    
    success "Migrations completed"
}

# Deploy application
deploy_application() {
    log "Deploying application..."
    
    # Stop existing containers gracefully
    if docker-compose -f "$COMPOSE_FILE" --env-file "$ENV_FILE" ps | grep -q "Up"; then
        log "Stopping existing containers..."
        docker-compose -f "$COMPOSE_FILE" --env-file "$ENV_FILE" down --timeout 30
    fi
    
    # Start new containers
    log "Starting new containers..."
    docker-compose -f "$COMPOSE_FILE" --env-file "$ENV_FILE" up -d
    
    # Wait for application to be ready
    log "Waiting for application to be ready..."
    sleep 45
    
    success "Application deployed"
}

# Post-deployment tasks
post_deployment_tasks() {
    log "Running post-deployment tasks..."
    
    # Clear and cache configuration
    docker-compose -f "$COMPOSE_FILE" --env-file "$ENV_FILE" exec -T app \
        php artisan config:cache || warning "Config cache failed"
    
    docker-compose -f "$COMPOSE_FILE" --env-file "$ENV_FILE" exec -T app \
        php artisan route:cache || warning "Route cache failed"
    
    docker-compose -f "$COMPOSE_FILE" --env-file "$ENV_FILE" exec -T app \
        php artisan view:cache || warning "View cache failed"
    
    # Optimize autoloader
    docker-compose -f "$COMPOSE_FILE" --env-file "$ENV_FILE" exec -T app \
        composer dump-autoload --optimize || warning "Autoloader optimization failed"
    
    success "Post-deployment tasks completed"
}

# Health check
health_check() {
    log "Performing health check..."
    
    # Check if containers are running
    if ! docker-compose -f "$COMPOSE_FILE" --env-file "$ENV_FILE" ps | grep -q "Up"; then
        error "Containers are not running"
    fi
    
    # Check application health endpoint
    local max_attempts=10
    local attempt=1
    
    while [[ $attempt -le $max_attempts ]]; do
        if curl -f http://localhost/health > /dev/null 2>&1; then
            success "Health check passed"
            return 0
        fi
        
        log "Health check attempt $attempt/$max_attempts failed, retrying..."
        sleep 10
        ((attempt++))
    done
    
    error "Health check failed after $max_attempts attempts"
}

# Cleanup old images
cleanup() {
    log "Cleaning up old Docker images..."
    
    # Remove unused images
    docker image prune -f || warning "Image cleanup failed"
    
    # Remove old backups (keep last 5)
    if [[ -d "$BACKUP_DIR" ]]; then
        cd "$BACKUP_DIR"
        ls -t backup-* | tail -n +6 | xargs -r rm -f
        success "Old backups cleaned up"
    fi
}

# Rollback function
rollback() {
    error "Deployment failed, initiating rollback..."
    
    # Get latest backup
    local latest_backup=$(ls -t "$BACKUP_DIR"/backup-*-database.sql 2>/dev/null | head -n 1)
    
    if [[ -n "$latest_backup" ]]; then
        log "Rolling back to latest backup..."
        
        # Restore database
        docker-compose -f "$COMPOSE_FILE" --env-file "$ENV_FILE" exec -T db mysql \
            -u root -p"${DB_ROOT_PASSWORD}" "${DB_DATABASE}" < "$latest_backup"
        
        # Restart containers
        docker-compose -f "$COMPOSE_FILE" --env-file "$ENV_FILE" restart
        
        warning "Rollback completed"
    else
        error "No backup found for rollback"
    fi
}

# Main deployment process
main() {
    log "Starting NHIT deployment process..."
    
    # Change to deployment directory
    cd "$DEPLOY_DIR" || error "Failed to change to deployment directory"
    
    # Set trap for rollback on error
    trap rollback ERR
    
    # Execute deployment steps
    pre_deployment_checks
    create_backup
    pull_images
    run_migrations
    deploy_application
    post_deployment_tasks
    health_check
    cleanup
    
    # Remove trap
    trap - ERR
    
    success "🎉 Deployment completed successfully!"
    log "Application is now running at: $(grep APP_URL $ENV_FILE | cut -d'=' -f2)"
}

# Execute main function
main "$@"
