#!/bin/bash

# Optimized Deployment Script for Laravel Application
# This script automates the deployment process with performance optimizations

set -e

# Configuration
APP_NAME="nhit"
APP_PATH="/var/www/html/nhit"
BACKUP_PATH="/var/backups/nhit"
LOG_FILE="/var/log/nhit-deployment.log"
PHP_VERSION="8.2"
NGINX_CONFIG="/etc/nginx/sites-available/nhit"
APACHE_CONFIG="/etc/apache2/sites-available/nhit.conf"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Logging function
log() {
    echo -e "${GREEN}[$(date +'%Y-%m-%d %H:%M:%S')]${NC} $1" | tee -a $LOG_FILE
}

error() {
    echo -e "${RED}[$(date +'%Y-%m-%d %H:%M:%S')] ERROR:${NC} $1" | tee -a $LOG_FILE
    exit 1
}

warning() {
    echo -e "${YELLOW}[$(date +'%Y-%m-%d %H:%M:%S')] WARNING:${NC} $1" | tee -a $LOG_FILE
}

# Check if running as root
if [ "$EUID" -ne 0 ]; then
    error "Please run as root"
fi

log "Starting deployment process for $APP_NAME"

# Create backup
create_backup() {
    log "Creating backup..."
    mkdir -p $BACKUP_PATH
    tar -czf "$BACKUP_PATH/backup-$(date +%Y%m%d-%H%M%S).tar.gz" -C $APP_PATH .
    log "Backup created successfully"
}

# Install dependencies
install_dependencies() {
    log "Installing system dependencies..."
    
    # Update package list
    apt-get update
    
    # Install essential packages
    apt-get install -y \
        nginx \
        apache2 \
        php$PHP_VERSION \
        php$PHP_VERSION-fpm \
        php$PHP_VERSION-mysql \
        php$PHP_VERSION-redis \
        php$PHP_VERSION-memcached \
        php$PHP_VERSION-gd \
        php$PHP_VERSION-curl \
        php$PHP_VERSION-zip \
        php$PHP_VERSION-mbstring \
        php$PHP_VERSION-xml \
        php$PHP_VERSION-bcmath \
        php$PHP_VERSION-intl \
        php$PHP_VERSION-imagick \
        redis-server \
        memcached \
        mysql-server \
        git \
        curl \
        unzip \
        supervisor \
        htop \
        iotop \
        nethogs
    
    log "Dependencies installed successfully"
}

# Configure PHP
configure_php() {
    log "Configuring PHP..."
    
    # Copy optimized PHP configuration
    cp setup/php_optimized.ini /etc/php/$PHP_VERSION/fpm/conf.d/99-optimized.ini
    cp setup/php_optimized.ini /etc/php/$PHP_VERSION/cli/conf.d/99-optimized.ini
    
    # Restart PHP-FPM
    systemctl restart php$PHP_VERSION-fpm
    
    log "PHP configured successfully"
}

# Configure Redis
configure_redis() {
    log "Configuring Redis..."
    
    # Enable Redis persistence
    sed -i 's/^save /#save /' /etc/redis/redis.conf
    echo "save 900 1" >> /etc/redis/redis.conf
    echo "save 300 10" >> /etc/redis/redis.conf
    echo "save 60 10000" >> /etc/redis/redis.conf
    
    # Optimize Redis memory
    echo "maxmemory 256mb" >> /etc/redis/redis.conf
    echo "maxmemory-policy allkeys-lru" >> /etc/redis/redis.conf
    
    # Start Redis
    systemctl enable redis-server
    systemctl start redis-server
    
    log "Redis configured successfully"
}

# Configure MySQL
configure_mysql() {
    log "Configuring MySQL..."
    
    # Start MySQL
    systemctl enable mysql
    systemctl start mysql
    
    # Run optimization SQL
    mysql -u root -p < setup/optimization_migration.sql
    
    log "MySQL configured successfully"
}

# Configure Nginx
configure_nginx() {
    log "Configuring Nginx..."
    
    # Copy optimized Nginx configuration
    cp setup/nginx_optimized.conf $NGINX_CONFIG
    
    # Enable site
    ln -sf $NGINX_CONFIG /etc/nginx/sites-enabled/
    
    # Test configuration
    nginx -t || error "Nginx configuration test failed"
    
    # Restart Nginx
    systemctl enable nginx
    systemctl restart nginx
    
    log "Nginx configured successfully"
}

# Configure Apache (alternative)
configure_apache() {
    log "Configuring Apache..."
    
    # Copy optimized Apache configuration
    cp setup/apache_optimized.conf $APACHE_CONFIG
    
    # Enable modules
    a2enmod rewrite
    a2enmod ssl
    a2enmod headers
    a2enmod deflate
    a2enmod expires
    
    # Enable site
    a2ensite nhit
    
    # Test configuration
    apache2ctl configtest || error "Apache configuration test failed"
    
    # Restart Apache
    systemctl enable apache2
    systemctl restart apache2
    
    log "Apache configured successfully"
}

# Deploy application
deploy_application() {
    log "Deploying application..."
    
    # Set proper permissions
    chown -R www-data:www-data $APP_PATH
    chmod -R 755 $APP_PATH
    chmod -R 775 $APP_PATH/storage
    chmod -R 775 $APP_PATH/bootstrap/cache
    
    # Install Composer dependencies
    cd $APP_PATH
    composer install --no-dev --optimize-autoloader
    
    # Install NPM dependencies
    npm install --production
    
    # Build assets
    npm run build
    
    # Clear and cache configurations
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan event:cache
    
    # Run migrations
    php artisan migrate --force
    
    # Seed database (if needed)
    # php artisan db:seed --force
    
    log "Application deployed successfully"
}

# Setup monitoring
setup_monitoring() {
    log "Setting up monitoring..."
    
    # Create monitoring directory
    mkdir -p /var/log/nhit-monitoring
    
    # Copy monitoring setup
    cp setup/monitoring_setup.php $APP_PATH/app/Services/PerformanceMonitoringService.php
    
    # Setup log rotation
    cat > /etc/logrotate.d/nhit << EOF
/var/log/nhit-deployment.log {
    daily
    missingok
    rotate 30
    compress
    delaycompress
    notifempty
    create 644 root root
}
EOF
    
    log "Monitoring setup completed"
}

# Setup cron jobs
setup_cron() {
    log "Setting up cron jobs..."
    
    # Add Laravel scheduler
    (crontab -l 2>/dev/null; echo "* * * * * cd $APP_PATH && php artisan schedule:run >> /dev/null 2>&1") | crontab -
    
    # Add performance monitoring
    (crontab -l 2>/dev/null; echo "*/5 * * * * cd $APP_PATH && php artisan monitor:performance >> /dev/null 2>&1") | crontab -
    
    # Add cache cleanup
    (crontab -l 2>/dev/null; echo "0 2 * * * cd $APP_PATH && php artisan cache:clear >> /dev/null 2>&1") | crontab -
    
    log "Cron jobs setup completed"
}

# Setup SSL (Let's Encrypt)
setup_ssl() {
    log "Setting up SSL..."
    
    # Install Certbot
    apt-get install -y certbot python3-certbot-nginx
    
    # Get SSL certificate
    certbot --nginx -d your-domain.com -d www.your-domain.com --non-interactive --agree-tos --email admin@your-domain.com
    
    # Setup auto-renewal
    (crontab -l 2>/dev/null; echo "0 12 * * * /usr/bin/certbot renew --quiet") | crontab -
    
    log "SSL setup completed"
}

# Performance optimization
optimize_performance() {
    log "Optimizing performance..."
    
    # Enable OPcache
    echo "opcache.enable=1" >> /etc/php/$PHP_VERSION/fpm/conf.d/99-optimized.ini
    echo "opcache.memory_consumption=256" >> /etc/php/$PHP_VERSION/fpm/conf.d/99-optimized.ini
    echo "opcache.max_accelerated_files=10000" >> /etc/php/$PHP_VERSION/fpm/conf.d/99-optimized.ini
    
    # Optimize MySQL
    mysql -u root -p << EOF
SET GLOBAL innodb_buffer_pool_size = 1G;
SET GLOBAL innodb_log_file_size = 256M;
SET GLOBAL innodb_flush_log_at_trx_commit = 2;
SET GLOBAL innodb_flush_method = O_DIRECT;
EOF
    
    # Restart services
    systemctl restart php$PHP_VERSION-fpm
    systemctl restart mysql
    
    log "Performance optimization completed"
}

# Main deployment function
main() {
    log "Starting deployment process..."
    
    # Create backup
    create_backup
    
    # Install dependencies
    install_dependencies
    
    # Configure services
    configure_php
    configure_redis
    configure_mysql
    configure_nginx
    # configure_apache  # Uncomment if using Apache instead of Nginx
    
    # Deploy application
    deploy_application
    
    # Setup monitoring
    setup_monitoring
    
    # Setup cron jobs
    setup_cron
    
    # Setup SSL (optional)
    # setup_ssl  # Uncomment if you want to setup SSL
    
    # Optimize performance
    optimize_performance
    
    log "Deployment completed successfully!"
    log "Application is now running at: https://your-domain.com"
    log "Monitoring dashboard: https://your-domain.com/monitoring"
}

# Run main function
main "$@"
