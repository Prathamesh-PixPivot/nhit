# NHIT Docker Production Deployment Guide

This guide provides comprehensive instructions for deploying the NHIT Laravel application using Docker in a production environment with CI/CD pipeline.

## 🏗️ Architecture Overview

The deployment consists of:
- **Multi-stage Docker build** for optimized production images
- **Docker Compose** for orchestrating services
- **GitHub Actions** for automated CI/CD
- **Nginx + PHP-FPM** for web serving
- **MySQL 8.0** for database
- **Redis** for caching and sessions
- **Automated backups** and monitoring

## 📋 Prerequisites

### Server Requirements
- **OS**: Ubuntu 20.04+ or CentOS 8+
- **RAM**: Minimum 2GB, Recommended 4GB+
- **Storage**: Minimum 20GB SSD
- **CPU**: 2+ cores recommended
- **Network**: Public IP with domain name

### Required Software
- Docker 20.10+
- Docker Compose 2.0+
- Git
- SSL Certificate (Let's Encrypt recommended)

## 🚀 Quick Start

### 1. Server Setup

Run the automated server setup script:

```bash
# Download and run server setup
curl -fsSL https://raw.githubusercontent.com/your-repo/nhit/main/deploy/server-setup.sh | bash
```

Or manually:

```bash
# Clone repository
git clone https://github.com/your-repo/nhit.git
cd nhit

# Make script executable and run
chmod +x deploy/server-setup.sh
./deploy/server-setup.sh
```

### 2. Configure Environment

```bash
# Copy environment template
cp .env.production.example .env.prod

# Edit configuration
nano .env.prod
```

**Required Configuration:**
- `APP_KEY`: Generate with `php artisan key:generate --show`
- `APP_URL`: Your domain (https://yourdomain.com)
- `DB_*`: Database credentials
- `REDIS_PASSWORD`: Secure Redis password
- `DOCKER_IMAGE`: Your Docker image URL

### 3. GitHub Repository Setup

#### Configure Secrets
Go to your GitHub repository → Settings → Secrets and variables → Actions

**Required Secrets:**
```
SSH_PRIVATE_KEY=your_private_key
SERVER_HOST=your.server.ip
SERVER_USER=deploy
APP_KEY=base64:your_app_key
DB_DATABASE=nhit_production
DB_USERNAME=nhit_user
DB_PASSWORD=secure_password
DB_ROOT_PASSWORD=secure_root_password
REDIS_PASSWORD=secure_redis_password
```

**Required Variables:**
```
APP_URL=https://yourdomain.com
```

### 4. Deploy

Push to main branch to trigger automatic deployment:

```bash
git push origin main
```

Or deploy manually:

```bash
# On your server
cd /opt/nhit
./deploy.sh
```

## 🔧 Configuration Details

### Docker Services

#### Application (app)
- **Image**: Multi-stage build with Node.js assets and PHP
- **Ports**: 80 (HTTP)
- **Features**: Nginx + PHP-FPM, optimized for production
- **Health Check**: `/health` endpoint

#### Database (db)
- **Image**: MySQL 8.0
- **Port**: 3306
- **Features**: Optimized configuration, automated backups
- **Persistence**: Named volume `db_data`

#### Cache (redis)
- **Image**: Redis 7 Alpine
- **Port**: 6379
- **Features**: Password protected, persistent storage
- **Persistence**: Named volume `redis_data`

#### Queue Worker (queue)
- **Image**: Same as app
- **Command**: `php artisan queue:work`
- **Features**: Auto-restart, error handling

#### Scheduler (scheduler)
- **Image**: Same as app
- **Command**: Laravel scheduler
- **Features**: Runs every minute

### Environment Variables

#### Application Settings
```env
APP_NAME=NHIT
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
```

#### Database Settings
```env
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=nhit_production
DB_USERNAME=nhit_user
DB_PASSWORD=secure_password
```

#### Cache & Session
```env
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
REDIS_HOST=redis
REDIS_PASSWORD=secure_password
```

## 🔄 CI/CD Pipeline

### Workflow Stages

1. **Test Stage**
   - PHP 8.2 with extensions
   - MySQL 8.0 service
   - Composer install
   - NPM build
   - Laravel tests
   - Code coverage

2. **Build Stage**
   - Multi-platform Docker build (amd64, arm64)
   - Push to GitHub Container Registry
   - Image optimization and caching

3. **Deploy Stage**
   - SSH to production server
   - Copy deployment files
   - Create environment configuration
   - Run deployment script
   - Health checks
   - Rollback on failure

### Deployment Features

- **Zero-downtime deployment**
- **Automatic database migrations**
- **Configuration caching**
- **Health checks**
- **Automatic rollback**
- **Backup creation**

## 🛠️ Management Commands

### Application Management

```bash
# View logs
docker-compose -f docker-compose.prod.yml logs -f app

# Run artisan commands
docker-compose -f docker-compose.prod.yml exec app php artisan migrate

# Access application shell
docker-compose -f docker-compose.prod.yml exec app bash

# Restart services
docker-compose -f docker-compose.prod.yml restart
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

### Monitoring

```bash
# Check container status
docker-compose -f docker-compose.prod.yml ps

# View resource usage
docker stats

# Check application health
curl https://yourdomain.com/health
```

## 🔒 Security Features

### Application Security
- **HTTPS enforcement**
- **Security headers** (CSP, HSTS, etc.)
- **Environment isolation**
- **Secure session handling**
- **Input validation**

### Infrastructure Security
- **Firewall configuration**
- **Non-root containers**
- **Secret management**
- **Network isolation**
- **Regular security updates**

### Database Security
- **Strong passwords**
- **Network isolation**
- **Encrypted connections**
- **Regular backups**
- **Access logging**

## 📊 Monitoring & Logging

### Application Monitoring
- **Health check endpoint**: `/health`
- **Application logs**: `/var/log/nhit-deploy.log`
- **Laravel logs**: `storage/logs/`
- **Nginx logs**: Container logs

### System Monitoring
- **Container health checks**
- **Resource usage monitoring**
- **Disk space alerts**
- **Automated restarts**

### Log Management
- **Log rotation** configured
- **Centralized logging** via Docker
- **Error tracking** in Laravel
- **Access logs** in Nginx

## 🔄 Backup & Recovery

### Automated Backups
- **Database backups**: Daily via cron
- **Storage backups**: Application files
- **Configuration backups**: Environment files
- **Retention policy**: 30 days

### Manual Backup
```bash
# Create full backup
./deploy.sh backup

# Restore from backup
./deploy.sh restore backup-20231201-120000
```

### Disaster Recovery
1. **Server failure**: Restore on new server
2. **Database corruption**: Restore from backup
3. **Application issues**: Rollback deployment
4. **Data loss**: Point-in-time recovery

## 🚨 Troubleshooting

### Common Issues

#### Container Won't Start
```bash
# Check logs
docker-compose -f docker-compose.prod.yml logs app

# Check configuration
docker-compose -f docker-compose.prod.yml config

# Restart services
docker-compose -f docker-compose.prod.yml restart
```

#### Database Connection Issues
```bash
# Check database status
docker-compose -f docker-compose.prod.yml ps db

# Test connection
docker-compose -f docker-compose.prod.yml exec app php artisan tinker
>>> DB::connection()->getPdo();
```

#### Performance Issues
```bash
# Check resource usage
docker stats

# Optimize Laravel
docker-compose -f docker-compose.prod.yml exec app php artisan optimize

# Clear caches
docker-compose -f docker-compose.prod.yml exec app php artisan cache:clear
```

### Emergency Procedures

#### Rollback Deployment
```bash
# Automatic rollback (if deployment fails)
# Manual rollback
cd /opt/nhit
./deploy.sh rollback
```

#### Scale Services
```bash
# Scale queue workers
docker-compose -f docker-compose.prod.yml up -d --scale queue=3
```

## 📈 Performance Optimization

### Application Optimization
- **OPcache enabled**
- **Configuration caching**
- **Route caching**
- **View caching**
- **Autoloader optimization**

### Database Optimization
- **Query optimization**
- **Index optimization**
- **Connection pooling**
- **Buffer pool tuning**

### Caching Strategy
- **Redis for sessions**
- **Redis for cache**
- **Static asset caching**
- **Browser caching**

## 🔧 Maintenance

### Regular Tasks
- **Update Docker images** (monthly)
- **Security patches** (as needed)
- **Database optimization** (weekly)
- **Log cleanup** (automated)
- **Backup verification** (weekly)

### Update Procedure
```bash
# Update application
git pull origin main
docker-compose -f docker-compose.prod.yml pull
docker-compose -f docker-compose.prod.yml up -d

# Update system
sudo apt update && sudo apt upgrade -y
```

## 📞 Support

### Getting Help
- **Documentation**: This file and inline comments
- **Logs**: Check application and system logs
- **Health checks**: Monitor `/health` endpoint
- **Community**: Laravel and Docker communities

### Emergency Contacts
- **System Administrator**: [Your contact]
- **Development Team**: [Your contact]
- **Hosting Provider**: [Your contact]

---

## 📝 Changelog

### Version 1.0.0
- Initial production deployment setup
- Docker containerization
- CI/CD pipeline implementation
- Monitoring and backup systems
- Security hardening
- Documentation

---

**Last Updated**: December 2024  
**Version**: 1.0.0  
**Maintainer**: NHIT Development Team
