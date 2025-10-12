#!/bin/bash

# NHIT Minimal Server Setup for Image-Only Deployment
# This script prepares a server to receive and run Docker images only

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

log() {
    echo -e "${BLUE}[$(date +'%Y-%m-%d %H:%M:%S')]${NC} $1"
}

error() {
    echo -e "${RED}[ERROR]${NC} $1"
    exit 1
}

success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

# Update system
update_system() {
    log "Updating system packages..."
    
    if command -v apt-get > /dev/null; then
        sudo apt-get update && sudo apt-get upgrade -y
        sudo apt-get install -y curl wget git unzip
    elif command -v yum > /dev/null; then
        sudo yum update -y
        sudo yum install -y curl wget git unzip
    else
        error "Unsupported package manager"
    fi
    
    success "System updated"
}

# Install Docker
install_docker() {
    log "Installing Docker..."
    
    if command -v docker > /dev/null; then
        warning "Docker is already installed"
        return 0
    fi
    
    # Install Docker using official script
    curl -fsSL https://get.docker.com -o get-docker.sh
    sudo sh get-docker.sh
    rm get-docker.sh
    
    # Add current user to docker group
    sudo usermod -aG docker $USER
    
    # Start and enable Docker
    sudo systemctl start docker
    sudo systemctl enable docker
    
    success "Docker installed"
}

# Install Docker Compose
install_docker_compose() {
    log "Installing Docker Compose..."
    
    if command -v docker-compose > /dev/null; then
        warning "Docker Compose is already installed"
        return 0
    fi
    
    # Install Docker Compose
    sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
    sudo chmod +x /usr/local/bin/docker-compose
    
    # Create symlink
    sudo ln -sf /usr/local/bin/docker-compose /usr/bin/docker-compose
    
    success "Docker Compose installed"
}

# Setup firewall
setup_firewall() {
    log "Setting up firewall..."
    
    if command -v ufw > /dev/null; then
        # Ubuntu/Debian
        sudo ufw --force enable
        sudo ufw allow ssh
        sudo ufw allow 80/tcp
        sudo ufw allow 443/tcp
        success "UFW firewall configured"
    elif command -v firewall-cmd > /dev/null; then
        # CentOS/RHEL
        sudo systemctl start firewalld
        sudo systemctl enable firewalld
        sudo firewall-cmd --permanent --add-service=ssh
        sudo firewall-cmd --permanent --add-service=http
        sudo firewall-cmd --permanent --add-service=https
        sudo firewall-cmd --reload
        success "Firewalld configured"
    else
        warning "No supported firewall found"
    fi
}

# Setup directories
setup_directories() {
    log "Setting up deployment directories..."
    
    sudo mkdir -p /opt/nhit
    sudo mkdir -p /opt/nhit/backups
    sudo mkdir -p /var/log
    
    # Set ownership to current user
    sudo chown -R $USER:$USER /opt/nhit
    sudo chmod -R 755 /opt/nhit
    
    success "Directories created"
}

# Create deployment files on server
create_deployment_files() {
    log "Creating deployment configuration files..."
    
    # Create docker-compose.prod.yml
    cat > /opt/nhit/docker-compose.prod.yml << 'EOF'
version: '3.8'

services:
  app:
    image: ${DOCKER_IMAGE:-nhit-app:latest}
    container_name: nhit-app
    restart: unless-stopped
    ports:
      - "${APP_PORT:-80}:80"
    environment:
      - APP_ENV=production
      - APP_DEBUG=false
      - APP_KEY=${APP_KEY}
      - APP_URL=${APP_URL}
      - DB_CONNECTION=${DB_CONNECTION:-mysql}
      - DB_HOST=${DB_HOST:-db}
      - DB_PORT=${DB_PORT:-3306}
      - DB_DATABASE=${DB_DATABASE}
      - DB_USERNAME=${DB_USERNAME}
      - DB_PASSWORD=${DB_PASSWORD}
      - REDIS_HOST=${REDIS_HOST:-redis}
      - REDIS_PORT=${REDIS_PORT:-6379}
      - REDIS_PASSWORD=${REDIS_PASSWORD}
      - CACHE_DRIVER=${CACHE_DRIVER:-redis}
      - SESSION_DRIVER=${SESSION_DRIVER:-redis}
      - QUEUE_CONNECTION=${QUEUE_CONNECTION:-redis}
    volumes:
      - storage_data:/var/www/html/storage/app
      - logs_data:/var/www/html/storage/logs
    depends_on:
      - db
      - redis
    networks:
      - nhit-network
    healthcheck:
      test: ["CMD", "curl", "-f", "http://localhost/health"]
      interval: 30s
      timeout: 10s
      retries: 3
      start_period: 40s

  db:
    image: mysql:8.0
    container_name: nhit-db
    restart: unless-stopped
    environment:
      - MYSQL_ROOT_PASSWORD=${DB_ROOT_PASSWORD}
      - MYSQL_DATABASE=${DB_DATABASE}
      - MYSQL_USER=${DB_USERNAME}
      - MYSQL_PASSWORD=${DB_PASSWORD}
    volumes:
      - db_data:/var/lib/mysql
    ports:
      - "${DB_EXTERNAL_PORT:-3306}:3306"
    networks:
      - nhit-network
    healthcheck:
      test: ["CMD", "mysqladmin", "ping", "-h", "localhost"]
      interval: 30s
      timeout: 10s
      retries: 5
      start_period: 30s

  redis:
    image: redis:7-alpine
    container_name: nhit-redis
    restart: unless-stopped
    command: redis-server --appendonly yes --requirepass ${REDIS_PASSWORD}
    volumes:
      - redis_data:/data
    ports:
      - "${REDIS_EXTERNAL_PORT:-6379}:6379"
    networks:
      - nhit-network
    healthcheck:
      test: ["CMD", "redis-cli", "ping"]
      interval: 30s
      timeout: 10s
      retries: 3

  queue:
    image: ${DOCKER_IMAGE:-nhit-app:latest}
    container_name: nhit-queue
    restart: unless-stopped
    command: php artisan queue:work --verbose --tries=3 --timeout=90
    environment:
      - APP_ENV=production
      - APP_DEBUG=false
      - APP_KEY=${APP_KEY}
      - DB_CONNECTION=${DB_CONNECTION:-mysql}
      - DB_HOST=${DB_HOST:-db}
      - DB_PORT=${DB_PORT:-3306}
      - DB_DATABASE=${DB_DATABASE}
      - DB_USERNAME=${DB_USERNAME}
      - DB_PASSWORD=${DB_PASSWORD}
      - REDIS_HOST=${REDIS_HOST:-redis}
      - REDIS_PORT=${REDIS_PORT:-6379}
      - REDIS_PASSWORD=${REDIS_PASSWORD}
      - CACHE_DRIVER=${CACHE_DRIVER:-redis}
      - SESSION_DRIVER=${SESSION_DRIVER:-redis}
      - QUEUE_CONNECTION=${QUEUE_CONNECTION:-redis}
    volumes:
      - storage_data:/var/www/html/storage/app
      - logs_data:/var/www/html/storage/logs
    depends_on:
      - db
      - redis
    networks:
      - nhit-network

  scheduler:
    image: ${DOCKER_IMAGE:-nhit-app:latest}
    container_name: nhit-scheduler
    restart: unless-stopped
    command: sh -c "while true; do php artisan schedule:run --verbose --no-interaction; sleep 60; done"
    environment:
      - APP_ENV=production
      - APP_DEBUG=false
      - APP_KEY=${APP_KEY}
      - DB_CONNECTION=${DB_CONNECTION:-mysql}
      - DB_HOST=${DB_HOST:-db}
      - DB_PORT=${DB_PORT:-3306}
      - DB_DATABASE=${DB_DATABASE}
      - DB_USERNAME=${DB_USERNAME}
      - DB_PASSWORD=${DB_PASSWORD}
      - REDIS_HOST=${REDIS_HOST:-redis}
      - REDIS_PORT=${REDIS_PORT:-6379}
      - REDIS_PASSWORD=${REDIS_PASSWORD}
      - CACHE_DRIVER=${CACHE_DRIVER:-redis}
      - SESSION_DRIVER=${SESSION_DRIVER:-redis}
      - QUEUE_CONNECTION=${QUEUE_CONNECTION:-redis}
    volumes:
      - storage_data:/var/www/html/storage/app
      - logs_data:/var/www/html/storage/logs
    depends_on:
      - db
      - redis
    networks:
      - nhit-network

volumes:
  db_data:
    driver: local
  redis_data:
    driver: local
  storage_data:
    driver: local
  logs_data:
    driver: local

networks:
  nhit-network:
    driver: bridge
EOF

    # Create environment template
    cat > /opt/nhit/.env.prod.example << 'EOF'
# NHIT Production Environment Configuration
APP_NAME=NHIT
APP_ENV=production
APP_KEY=base64:YOUR_32_CHARACTER_SECRET_KEY_HERE
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=nhit_production
DB_USERNAME=nhit_user
DB_PASSWORD=your_secure_database_password
DB_ROOT_PASSWORD=your_secure_root_password

# Redis Configuration
REDIS_HOST=redis
REDIS_PORT=6379
REDIS_PASSWORD=your_secure_redis_password

# Cache Configuration
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Docker Configuration
DOCKER_IMAGE=ghcr.io/your-username/nhit:latest
APP_PORT=80
EOF

    success "Deployment files created"
}

# Setup log rotation
setup_log_rotation() {
    log "Setting up log rotation..."
    
    sudo tee /etc/logrotate.d/nhit > /dev/null <<EOF
/var/log/nhit-deploy.log {
    daily
    missingok
    rotate 30
    compress
    delaycompress
    notifempty
    create 644 $USER $USER
}
EOF
    
    success "Log rotation configured"
}

# Setup monitoring
setup_monitoring() {
    log "Setting up basic monitoring..."
    
    # Create monitoring script
    sudo tee /usr/local/bin/nhit-monitor.sh > /dev/null <<'EOF'
#!/bin/bash

# NHIT Monitoring Script
LOG_FILE="/var/log/nhit-monitor.log"

log() {
    echo "[$(date +'%Y-%m-%d %H:%M:%S')] $1" >> "$LOG_FILE"
}

# Check if containers are running
if ! docker-compose -f /opt/nhit/docker-compose.prod.yml ps | grep -q "Up"; then
    log "ERROR: NHIT containers are not running"
    # Restart containers
    cd /opt/nhit && docker-compose -f docker-compose.prod.yml --env-file .env.prod up -d
    log "INFO: Attempted to restart containers"
fi

# Check disk space
DISK_USAGE=$(df / | awk 'NR==2 {print $5}' | sed 's/%//')
if [ "$DISK_USAGE" -gt 80 ]; then
    log "WARNING: Disk usage is ${DISK_USAGE}%"
fi

# Check memory usage
MEMORY_USAGE=$(free | awk 'NR==2{printf "%.0f", $3*100/$2}')
if [ "$MEMORY_USAGE" -gt 90 ]; then
    log "WARNING: Memory usage is ${MEMORY_USAGE}%"
fi
EOF

    sudo chmod +x /usr/local/bin/nhit-monitor.sh
    
    # Add to crontab
    (crontab -l 2>/dev/null; echo "*/5 * * * * /usr/local/bin/nhit-monitor.sh") | crontab -
    
    success "Monitoring configured"
}

# Main setup process
main() {
    log "Starting NHIT minimal server setup for image-only deployment..."
    
    update_system
    install_docker
    install_docker_compose
    setup_firewall
    setup_directories
    create_deployment_files
    setup_log_rotation
    setup_monitoring
    
    success "🎉 Server setup completed!"
    warning "Next steps:"
    warning "1. Copy the deploy-image.sh script to /opt/nhit/"
    warning "2. Configure /opt/nhit/.env.prod with your settings"
    warning "3. Set up GitHub secrets for automated deployment"
    warning "4. Configure your domain DNS to point to this server"
    warning "5. Set up SSL certificate (optional but recommended)"
    
    log "Server is ready to receive Docker images!"
}

# Execute main function
main "$@"
