#!/bin/bash

# NHIT Image-Only Deployment Script
# This script handles zero-downtime deployment using only Docker images
# No source code is transferred to the server

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

# Get the new image from command line argument
NEW_IMAGE="$1"

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

# Validate input
validate_input() {
    if [[ -z "$NEW_IMAGE" ]]; then
        error "Usage: $0 <docker-image>"
    fi
    
    log "Deploying image: $NEW_IMAGE"
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
    
    # Check if image exists
    if ! docker image inspect "$NEW_IMAGE" > /dev/null 2>&1; then
        log "Image not found locally, pulling..."
        docker pull "$NEW_IMAGE" || error "Failed to pull image: $NEW_IMAGE"
    fi
    
    success "Pre-deployment checks passed"
}

# Create backup
create_backup() {
    log "Creating backup..."
    
    mkdir -p "$BACKUP_DIR"
    BACKUP_NAME="backup-$(date +%Y%m%d-%H%M%S)"
    BACKUP_PATH="$BACKUP_DIR/$BACKUP_NAME"
    
    # Get current image info
    CURRENT_IMAGE=$(docker-compose -f "$COMPOSE_FILE" --env-file "$ENV_FILE" images -q app 2>/dev/null || echo "none")
    if [[ "$CURRENT_IMAGE" != "none" ]]; then
        echo "$CURRENT_IMAGE" > "$BACKUP_PATH-image.txt"
        log "Current image backed up: $CURRENT_IMAGE"
    fi
    
    # Backup database if running
    if docker-compose -f "$COMPOSE_FILE" --env-file "$ENV_FILE" ps db | grep -q "Up"; then
        log "Backing up database..."
        # Load environment variables
        export $(grep -v '^#' "$ENV_FILE" | xargs)
        docker-compose -f "$COMPOSE_FILE" --env-file "$ENV_FILE" exec -T db mysqldump \
            -u root -p"${DB_ROOT_PASSWORD}" "${DB_DATABASE}" > "$BACKUP_PATH-database.sql" 2>/dev/null || warning "Database backup failed"
        success "Database backup created: $BACKUP_PATH-database.sql"
    fi
    
    # Backup storage volumes
    log "Backing up storage volumes..."
    if docker volume ls | grep -q "nhit_storage_data"; then
        docker run --rm -v nhit_storage_data:/data -v "$BACKUP_DIR":/backup alpine \
            tar czf "/backup/$BACKUP_NAME-storage.tar.gz" -C /data . 2>/dev/null || warning "Storage backup failed"
    fi
    
    success "Backup created: $BACKUP_NAME"
}

# Update environment with new image
update_environment() {
    log "Updating environment with new image..."
    
    # Update the DOCKER_IMAGE in environment file
    if grep -q "DOCKER_IMAGE=" "$ENV_FILE"; then
        sed -i "s|DOCKER_IMAGE=.*|DOCKER_IMAGE=$NEW_IMAGE|" "$ENV_FILE"
    else
        echo "DOCKER_IMAGE=$NEW_IMAGE" >> "$ENV_FILE"
    fi
    
    success "Environment updated with new image"
}

# Run database migrations
run_migrations() {
    log "Running database migrations..."
    
    # Start database and redis if not running
    docker-compose -f "$COMPOSE_FILE" --env-file "$ENV_FILE" up -d db redis
    
    # Wait for database to be ready
    log "Waiting for database to be ready..."
    sleep 15
    
    # Run migrations using the new image
    docker-compose -f "$COMPOSE_FILE" --env-file "$ENV_FILE" run --rm app \
        php artisan migrate --force || error "Migration failed"
    
    success "Migrations completed"
}

# Deploy new image
deploy_new_image() {
    log "Deploying new image..."
    
    # Pull the new image
    log "Pulling new image: $NEW_IMAGE"
    docker pull "$NEW_IMAGE" || error "Failed to pull new image"
    
    # Stop application containers (keep db and redis running)
    log "Stopping application containers..."
    docker-compose -f "$COMPOSE_FILE" --env-file "$ENV_FILE" stop app queue scheduler 2>/dev/null || true
    
    # Remove old application containers
    docker-compose -f "$COMPOSE_FILE" --env-file "$ENV_FILE" rm -f app queue scheduler 2>/dev/null || true
    
    # Start new containers with updated image
    log "Starting new containers..."
    docker-compose -f "$COMPOSE_FILE" --env-file "$ENV_FILE" up -d
    
    # Wait for application to be ready
    log "Waiting for application to be ready..."
    sleep 30
    
    success "New image deployed"
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
    
    # Remove unused images (keep current and previous)
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
    local latest_backup=$(ls -t "$BACKUP_DIR"/backup-*-image.txt 2>/dev/null | head -n 1)
    
    if [[ -n "$latest_backup" && -f "$latest_backup" ]]; then
        local previous_image=$(cat "$latest_backup")
        log "Rolling back to previous image: $previous_image"
        
        # Update environment with previous image
        sed -i "s|DOCKER_IMAGE=.*|DOCKER_IMAGE=$previous_image|" "$ENV_FILE"
        
        # Restart containers with previous image
        docker-compose -f "$COMPOSE_FILE" --env-file "$ENV_FILE" down --timeout 30
        docker-compose -f "$COMPOSE_FILE" --env-file "$ENV_FILE" up -d
        
        warning "Rollback completed to image: $previous_image"
    else
        error "No previous image found for rollback"
    fi
}

# Main deployment process
main() {
    log "Starting NHIT image deployment process..."
    
    # Change to deployment directory
    cd "$DEPLOY_DIR" || error "Failed to change to deployment directory"
    
    # Set trap for rollback on error
    trap rollback ERR
    
    # Execute deployment steps
    validate_input
    pre_deployment_checks
    create_backup
    update_environment
    run_migrations
    deploy_new_image
    post_deployment_tasks
    health_check
    cleanup
    
    # Remove trap
    trap - ERR
    
    success "🎉 Image deployment completed successfully!"
    log "Deployed image: $NEW_IMAGE"
    log "Application is now running at: $(grep APP_URL $ENV_FILE | cut -d'=' -f2)"
}

# Execute main function
main "$@"
