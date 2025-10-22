# NHIT Image-Only Deployment Guide

Complete step-by-step guide for deploying NHIT Laravel application using **Docker images only** - no source code on the production server.

## 🎯 Overview

This deployment strategy:
- ✅ **Builds Docker images** in GitHub Actions
- ✅ **Pushes images** to GitHub Container Registry
- ✅ **Deploys only images** to production server
- ✅ **No source code** transferred to server
- ✅ **Zero-downtime deployment** with automatic rollback
- ✅ **Fully automated CI/CD** pipeline

---

## 📋 Prerequisites

### Server Requirements
- **OS**: Ubuntu 20.04+ or CentOS 8+
- **RAM**: Minimum 2GB, Recommended 4GB+
- **Storage**: Minimum 20GB SSD
- **CPU**: 2+ cores recommended
- **Network**: Public IP with SSH access

### Local Requirements
- SSH access to your Linux server
- GitHub repository with admin access
- Domain name (optional but recommended)

---

## 🚀 Step-by-Step Setup

### Step 1: Prepare Your Server

#### Option A: Automated Setup (Recommended)
```bash
# SSH into your server
ssh nhitAdmin@192.168.20.1

# Download and run the setup script
curl -fsSL https://raw.githubusercontent.com/Prathamesh-PixPivot/nhit/main/deploy/server-setup-minimal.sh -o setup.sh
chmod +x setup.sh
./setup.sh
```

#### Option B: Manual Setup
```bash
# Update system
sudo apt update && sudo apt upgrade -y
sudo apt install -y curl wget git unzip

# Install Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh
sudo usermod -aG docker $USER

# Install Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose

# Create directories
sudo mkdir -p /opt/nhit/backups
sudo chown -R $USER:$USER /opt/nhit

# Reboot to apply Docker group changes
sudo reboot
```

### Step 2: Copy Deployment Files to Server

```bash
# From your local machine, copy the deployment script
scp deploy/deploy-image.sh nhitAdmin@192.168.20.1:/opt/nhit/
scp docker-compose.prod.yml nhitAdmin@192.168.20.1:/opt/nhit/

# SSH into server and make script executable
ssh nhitAdmin@192.168.20.1
chmod +x /opt/nhit/deploy-image.sh
```

### Step 3: Configure Environment on Server

```bash
# SSH into your server
ssh nhitAdmin@192.168.20.1

# Create production environment file
nano /opt/nhit/.env.prod
```

**Copy and customize this configuration:**
```env
# Application
APP_NAME=NHIT
APP_ENV=production
APP_KEY=base64:YOUR_32_CHARACTER_SECRET_KEY_HERE
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=nhit_production
DB_USERNAME=nhit_user
DB_PASSWORD=your_secure_database_password
DB_ROOT_PASSWORD=your_secure_root_password

# Redis
REDIS_HOST=redis
REDIS_PORT=6379
REDIS_PASSWORD=your_secure_redis_password

# Cache & Sessions
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Docker Image (will be updated automatically)
DOCKER_IMAGE=ghcr.io/Prathamesh-PixPivot/nhit:latest
APP_PORT=80
```

**Generate APP_KEY:**
```bash
# On your local machine (where you have Laravel)
php artisan key:generate --show
```

### Step 4: Configure GitHub Repository

#### 4.1 Enable GitHub Container Registry

1. Go to your GitHub repository
2. Click **Settings** → **Actions** → **General**
3. Under **Workflow permissions**, select **Read and write permissions**
4. Click **Save**

#### 4.2 Configure Repository Secrets

Go to **Settings** → **Secrets and variables** → **Actions** → **New repository secret**

**Required Secrets:**
```
SSH_PRIVATE_KEY=-----BEGIN OPENSSH PRIVATE KEY-----
your_private_key_content_here
-----END OPENSSH PRIVATE KEY-----

SERVER_HOST=your.server.ip.address
SERVER_USER=your-server-username
APP_KEY=base64:your_32_character_secret_key
DB_DATABASE=nhit_production
DB_USERNAME=nhit_user
DB_PASSWORD=your_secure_database_password
DB_ROOT_PASSWORD=your_secure_root_password
REDIS_PASSWORD=your_secure_redis_password
```

**Required Variables:**
Go to **Settings** → **Secrets and variables** → **Actions** → **Variables** tab
```
APP_URL=https://yourdomain.com
```

#### 4.3 Generate SSH Key Pair (if needed)

```bash
# On your local machine
ssh-keygen -t rsa -b 4096 -C "deployment@nhit"

# Copy public key to server
ssh-copy-id -i ~/.ssh/id_rsa.pub your-user@192.168.20.1

# Copy private key content for GitHub secret
cat ~/.ssh/id_rsa
```

### Step 5: Test Manual Deployment

Before setting up automation, test manual deployment:

```bash
# SSH into your server
ssh your-user@192.168.20.1

# Pull a test image (nginx for testing)
docker pull nginx:alpine

# Test the deployment script
cd /opt/nhit
./deploy-image.sh nginx:alpine
```

### Step 6: Deploy Your Application

#### 6.1 Push Code to Trigger Build

```bash
# From your local development machine
git add .
git commit -m "Setup image-only deployment"
git push origin main
```

#### 6.2 Monitor Deployment

1. Go to your GitHub repository
2. Click **Actions** tab
3. Watch the workflow progress:
   - ✅ **Test**: Runs Laravel tests
   - ✅ **Build**: Creates and pushes Docker image
   - ✅ **Deploy**: Deploys image to your server

#### 6.3 Verify Deployment

```bash
# Check if containers are running
ssh your-user@192.168.20.1 "docker ps"

# Check application health
curl http://192.168.20.1/health

# Check logs
ssh your-user@192.168.20.1 "docker logs nhit-app"
```

---

## 🔧 Management Commands

### Server Management

```bash
# SSH into your server
ssh your-user@192.168.20.1

# Check container status
cd /opt/nhit
docker-compose -f docker-compose.prod.yml ps

# View application logs
docker-compose -f docker-compose.prod.yml logs -f app

# Restart services
docker-compose -f docker-compose.prod.yml restart

# Stop all services
docker-compose -f docker-compose.prod.yml down

# Start all services
docker-compose -f docker-compose.prod.yml up -d
```

### Laravel Commands

```bash
# Run artisan commands
docker-compose -f docker-compose.prod.yml exec app php artisan migrate
docker-compose -f docker-compose.prod.yml exec app php artisan cache:clear
docker-compose -f docker-compose.prod.yml exec app php artisan config:cache

# Access application shell
docker-compose -f docker-compose.prod.yml exec app bash
```

### Database Management

```bash
# Access MySQL
docker-compose -f docker-compose.prod.yml exec db mysql -u root -p

# Create backup
docker-compose -f docker-compose.prod.yml exec db mysqldump -u root -p nhit_production > backup.sql

# Restore backup
docker-compose -f docker-compose.prod.yml exec -T db mysql -u root -p nhit_production < backup.sql
```

---

## 🔄 Deployment Workflow

### Automatic Deployment (Recommended)

1. **Make changes** to your code locally
2. **Commit and push** to main branch
3. **GitHub Actions automatically**:
   - Runs tests
   - Builds Docker image
   - Pushes to registry
   - Deploys to server
   - Runs health checks

### Manual Deployment

```bash
# Build and push image manually (from local machine)
docker build -t ghcr.io/Prathamesh-PixPivot/nhit:latest .
docker push ghcr.io/Prathamesh-PixPivot/nhit:latest

# Deploy on server
ssh your-user@192.168.20.1 "cd /opt/nhit && ./deploy-image.sh ghcr.io/Prathamesh-PixPivot/nhit:latest"
```

---

## 🚨 Troubleshooting

### Common Issues

#### 1. Container Won't Start
```bash
# Check logs
docker-compose -f docker-compose.prod.yml logs app

# Check image
docker images | grep nhit

# Restart containers
docker-compose -f docker-compose.prod.yml restart
```

#### 2. Database Connection Issues
```bash
# Check database container
docker-compose -f docker-compose.prod.yml logs db

# Test connection
docker-compose -f docker-compose.prod.yml exec app php artisan tinker
>>> DB::connection()->getPdo();
```

#### 3. Image Pull Issues
```bash
# Login to GitHub Container Registry
echo $GITHUB_TOKEN | docker login ghcr.io -u USERNAME --password-stdin

# Pull image manually
docker pull ghcr.io/Prathamesh-PixPivot/nhit:latest
```

#### 4. Permission Issues
```bash
# Fix ownership
sudo chown -R $USER:$USER /opt/nhit

# Fix permissions
chmod +x /opt/nhit/deploy-image.sh
```

### Emergency Procedures

#### Rollback to Previous Image
```bash
# Check backup directory
ls -la /opt/nhit/backups/

# Rollback (automatic if deployment fails)
cd /opt/nhit
./deploy-image.sh $(cat backups/backup-latest-image.txt)
```

#### Complete Reset
```bash
# Stop all containers
docker-compose -f docker-compose.prod.yml down

# Remove all containers and volumes
docker system prune -a --volumes

# Restart from scratch
docker-compose -f docker-compose.prod.yml up -d
```

---

## 📊 Monitoring & Maintenance

### Health Checks

```bash
# Application health
curl http://192.168.20.1/health

# Container health
docker ps --format "table {{.Names}}\t{{.Status}}"

# Resource usage
docker stats --no-stream
```

### Log Monitoring

```bash
# Application logs
tail -f /var/log/nhit-deploy.log

# Container logs
docker-compose -f docker-compose.prod.yml logs -f --tail=100

# System logs
journalctl -u docker -f
```

### Backup Verification

```bash
# List backups
ls -la /opt/nhit/backups/

# Test database backup
docker-compose -f docker-compose.prod.yml exec -T db mysql -u root -p nhit_production < /opt/nhit/backups/backup-latest-database.sql
```

---

## 🔒 Security Best Practices

### Server Security

```bash
# Update system regularly
sudo apt update && sudo apt upgrade -y

# Configure firewall
sudo ufw enable
sudo ufw allow ssh
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp

# Disable root login
sudo nano /etc/ssh/sshd_config
# Set: PermitRootLogin no
sudo systemctl restart sshd
```

### Application Security

- ✅ Use strong passwords for database and Redis
- ✅ Keep APP_KEY secret and secure
- ✅ Use HTTPS in production
- ✅ Regular security updates
- ✅ Monitor access logs

### Container Security

```bash
# Scan images for vulnerabilities
docker scout cves ghcr.io/Prathamesh-PixPivot/nhit:latest

# Update base images regularly
# (handled automatically in CI/CD)
```

---

## 📈 Performance Optimization

### Server Optimization

```bash
# Optimize Docker
echo '{"log-driver":"json-file","log-opts":{"max-size":"10m","max-file":"3"}}' | sudo tee /etc/docker/daemon.json
sudo systemctl restart docker

# System optimization
echo 'vm.swappiness=10' | sudo tee -a /etc/sysctl.conf
sudo sysctl -p
```

### Application Optimization

```bash
# Laravel optimizations (run after deployment)
docker-compose -f docker-compose.prod.yml exec app php artisan config:cache
docker-compose -f docker-compose.prod.yml exec app php artisan route:cache
docker-compose -f docker-compose.prod.yml exec app php artisan view:cache
docker-compose -f docker-compose.prod.yml exec app composer dump-autoload --optimize
```

---

## 📞 Support & Maintenance

### Regular Maintenance Tasks

**Daily:**
- [ ] Check application health: `curl http://192.168.20.1/health`
- [ ] Monitor resource usage: `docker stats --no-stream`

**Weekly:**
- [ ] Review logs: `tail -100 /var/log/nhit-deploy.log`
- [ ] Update system: `sudo apt update && sudo apt upgrade -y`
- [ ] Verify backups: `ls -la /opt/nhit/backups/`

**Monthly:**
- [ ] Update Docker images: Push to main branch
- [ ] Security audit: `docker scout cves`
- [ ] Performance review: Check response times

### Getting Help

1. **Check logs** first: Application, container, and system logs
2. **Review documentation**: This guide and Docker documentation
3. **Test manually**: Use manual deployment commands
4. **Check GitHub Actions**: Review workflow logs
5. **Verify configuration**: Environment files and secrets

---

## 📝 Quick Reference

### Essential Commands

```bash
# Deploy new image
./deploy-image.sh ghcr.io/Prathamesh-PixPivot/nhit:latest

# Check status
docker-compose -f docker-compose.prod.yml ps

# View logs
docker-compose -f docker-compose.prod.yml logs -f app

# Restart application
docker-compose -f docker-compose.prod.yml restart app

# Health check
curl http://localhost/health
```

### Important Files

| File | Location | Purpose |
|------|----------|---------|
| `docker-compose.prod.yml` | `/opt/nhit/` | Container orchestration |
| `.env.prod` | `/opt/nhit/` | Environment configuration |
| `deploy-image.sh` | `/opt/nhit/` | Deployment script |
| Deployment logs | `/var/log/nhit-deploy.log` | Deployment history |
| Backups | `/opt/nhit/backups/` | Database and image backups |

### GitHub Secrets Required

```
SSH_PRIVATE_KEY    # Your SSH private key
SERVER_HOST        # Server IP address
SERVER_USER        # SSH username
APP_KEY           # Laravel application key
DB_DATABASE       # Database name
DB_USERNAME       # Database user
DB_PASSWORD       # Database password
DB_ROOT_PASSWORD  # MySQL root password
REDIS_PASSWORD    # Redis password
```

---

**🎉 Congratulations!** Your NHIT application is now deployed using image-only deployment with full CI/CD automation. No source code ever touches your production server - only secure, tested Docker images.

---

**Last Updated**: December 2024  
**Version**: 1.0.0  
**Deployment Type**: Image-Only with CI/CD
