# NHIT Docker Quick Reference

## 🚀 Quick Commands

### Build & Deploy
```bash
# Build Docker image locally
docker build -t nhit-app:latest .

# Run production stack
docker-compose -f docker-compose.prod.yml --env-file .env.prod up -d

# Deploy with script
./deploy/deploy.sh
```

### Service Management
```bash
# Start services
docker-compose -f docker-compose.prod.yml up -d

# Stop services
docker-compose -f docker-compose.prod.yml down

# Restart specific service
docker-compose -f docker-compose.prod.yml restart app

# View logs
docker-compose -f docker-compose.prod.yml logs -f app
```

### Laravel Commands
```bash
# Run migrations
docker-compose -f docker-compose.prod.yml exec app php artisan migrate

# Clear cache
docker-compose -f docker-compose.prod.yml exec app php artisan cache:clear

# Run queue worker
docker-compose -f docker-compose.prod.yml exec app php artisan queue:work

# Access tinker
docker-compose -f docker-compose.prod.yml exec app php artisan tinker
```

### Database Operations
```bash
# Access MySQL
docker-compose -f docker-compose.prod.yml exec db mysql -u root -p

# Create backup
docker-compose -f docker-compose.prod.yml exec db mysqldump -u root -p nhit_production > backup.sql

# Import backup
docker-compose -f docker-compose.prod.yml exec -T db mysql -u root -p nhit_production < backup.sql
```

### Monitoring
```bash
# Check container status
docker-compose -f docker-compose.prod.yml ps

# View resource usage
docker stats

# Check health
curl http://localhost/health

# View system logs
tail -f /var/log/nhit-deploy.log
```

## 🔧 Environment Files

### Production (.env.prod)
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
DB_HOST=db
REDIS_HOST=redis
DOCKER_IMAGE=ghcr.io/username/nhit:latest
```

### GitHub Secrets
```
SSH_PRIVATE_KEY
SERVER_HOST
SERVER_USER
APP_KEY
DB_DATABASE
DB_USERNAME
DB_PASSWORD
DB_ROOT_PASSWORD
REDIS_PASSWORD
```

## 🐳 Docker Services

| Service | Port | Purpose |
|---------|------|---------|
| app | 80 | Main application (Nginx + PHP-FPM) |
| db | 3306 | MySQL database |
| redis | 6379 | Cache and sessions |
| queue | - | Background job processing |
| scheduler | - | Laravel task scheduling |

## 📁 Important Paths

| Path | Purpose |
|------|---------|
| `/opt/nhit/` | Deployment directory |
| `/opt/nhit/backups/` | Backup storage |
| `/var/log/nhit-deploy.log` | Deployment logs |
| `/var/www/html/storage/logs/` | Application logs |

## 🚨 Emergency Commands

### Rollback
```bash
cd /opt/nhit
./deploy.sh rollback
```

### Force Restart
```bash
docker-compose -f docker-compose.prod.yml down --timeout 30
docker-compose -f docker-compose.prod.yml up -d
```

### Emergency Backup
```bash
docker-compose -f docker-compose.prod.yml exec db mysqldump -u root -p --all-databases > emergency-backup.sql
```

### Check Disk Space
```bash
df -h
docker system df
docker system prune -f
```

## 🔍 Troubleshooting

### Container Issues
```bash
# Check container logs
docker-compose -f docker-compose.prod.yml logs app

# Inspect container
docker inspect nhit-app

# Execute shell in container
docker-compose -f docker-compose.prod.yml exec app bash
```

### Database Issues
```bash
# Check database connection
docker-compose -f docker-compose.prod.yml exec app php artisan tinker
>>> DB::connection()->getPdo();

# Check database status
docker-compose -f docker-compose.prod.yml exec db mysqladmin -u root -p status
```

### Performance Issues
```bash
# Check resource usage
docker stats --no-stream

# Check Laravel performance
docker-compose -f docker-compose.prod.yml exec app php artisan route:cache
docker-compose -f docker-compose.prod.yml exec app php artisan config:cache
docker-compose -f docker-compose.prod.yml exec app php artisan view:cache
```

## 📊 Health Checks

### Application Health
- **URL**: `http://localhost/health`
- **Expected**: `200 OK` with "healthy" response

### Service Health
```bash
# All services
docker-compose -f docker-compose.prod.yml ps

# Specific service health
docker-compose -f docker-compose.prod.yml exec app curl -f http://localhost/health
```

## 🔄 CI/CD Pipeline

### Manual Trigger
```bash
# Push to main branch
git push origin main

# Or create release
git tag v1.0.0
git push origin v1.0.0
```

### Pipeline Status
- **GitHub Actions**: Check repository Actions tab
- **Deployment logs**: `/var/log/nhit-deploy.log`

## 📋 Maintenance Checklist

### Daily
- [ ] Check application health
- [ ] Monitor resource usage
- [ ] Review error logs

### Weekly
- [ ] Verify backups
- [ ] Update dependencies
- [ ] Clean old Docker images
- [ ] Review security logs

### Monthly
- [ ] Update base images
- [ ] Security patches
- [ ] Performance review
- [ ] Backup testing

---

**Quick Help**: For detailed information, see `DEPLOYMENT.md`
