#!/bin/bash

# NHIT Server Setup Script
# This script prepares a fresh server for NHIT deployment

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

# Create deployment user
create_deployment_user() {
    log "Creating deployment user..."
    
    if id "deploy" &>/dev/null; then
        warning "Deploy user already exists"
        return 0
    fi
    
    # Create deploy user
    sudo useradd -m -s /bin/bash deploy
    sudo usermod -aG docker deploy
    sudo usermod -aG sudo deploy
    
    # Setup SSH directory
    sudo mkdir -p /home/deploy/.ssh
    sudo chmod 700 /home/deploy/.ssh
    sudo chown deploy:deploy /home/deploy/.ssh
    
    success "Deploy user created"
}

# Setup directories
setup_directories() {
    log "Setting up deployment directories..."
    
    sudo mkdir -p /opt/nhit
    sudo mkdir -p /opt/nhit/backups
    sudo mkdir -p /var/log
    
    sudo chown -R deploy:deploy /opt/nhit
    sudo chmod -R 755 /opt/nhit
    
    success "Directories created"
}

# Install SSL certificate (Let's Encrypt)
install_ssl() {
    log "Installing SSL certificate..."
    
    if command -v certbot > /dev/null; then
        warning "Certbot is already installed"
        return 0
    fi
    
    # Install certbot
    if command -v apt-get > /dev/null; then
        sudo apt-get install -y certbot
    elif command -v yum > /dev/null; then
        sudo yum install -y certbot
    fi
    
    success "Certbot installed"
    warning "Run 'sudo certbot certonly --standalone -d yourdomain.com' to get SSL certificate"
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
    create 644 deploy deploy
}

/opt/nhit/storage/logs/*.log {
    daily
    missingok
    rotate 30
    compress
    delaycompress
    notifempty
    create 644 deploy deploy
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
    log "Starting NHIT server setup..."
    
    update_system
    install_docker
    install_docker_compose
    setup_firewall
    create_deployment_user
    setup_directories
    install_ssl
    setup_log_rotation
    setup_monitoring
    
    success "🎉 Server setup completed!"
    warning "Please reboot the server to ensure all changes take effect"
    warning "Don't forget to:"
    warning "1. Add your SSH public key to /home/deploy/.ssh/authorized_keys"
    warning "2. Configure your domain DNS to point to this server"
    warning "3. Run certbot to get SSL certificate"
    warning "4. Update GitHub secrets with server details"
}

# Execute main function
main "$@"
