# NHIT Quick Start - Image-Only Deployment

**Deploy NHIT in 15 minutes with zero source code on server!**

## 🚀 Super Quick Setup

### 1. Server Setup (5 minutes)
```bash
# SSH into your Linux server
ssh your-user@your-server-ip

# Run one-command setup
curl -fsSL https://raw.githubusercontent.com/your-username/nhit/main/deploy/server-setup-minimal.sh | bash

# Reboot server
sudo reboot
```

### 2. Copy Deployment Files (2 minutes)
```bash
# From your local machine
scp deploy/deploy-image.sh your-user@your-server-ip:/opt/nhit/
scp docker-compose.prod.yml your-user@your-server-ip:/opt/nhit/

# Make executable
ssh your-user@your-server-ip "chmod +x /opt/nhit/deploy-image.sh"
```

### 3. Configure Environment (3 minutes)
```bash
# SSH into server
ssh your-user@your-server-ip

# Create environment file
cat > /opt/nhit/.env.prod << 'EOF'
APP_NAME=NHIT
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=nhit_production
DB_USERNAME=nhit_user
DB_PASSWORD=secure_db_password
DB_ROOT_PASSWORD=secure_root_password

REDIS_HOST=redis
REDIS_PORT=6379
REDIS_PASSWORD=secure_redis_password

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

DOCKER_IMAGE=ghcr.io/your-username/nhit:latest
APP_PORT=80
EOF
```

### 4. GitHub Setup (3 minutes)

**Add these secrets** in GitHub → Settings → Secrets and variables → Actions:

```
SSH_PRIVATE_KEY=your_ssh_private_key
SERVER_HOST=your.server.ip
SERVER_USER=your-username
APP_KEY=base64:your_app_key
DB_DATABASE=nhit_production
DB_USERNAME=nhit_user
DB_PASSWORD=secure_db_password
DB_ROOT_PASSWORD=secure_root_password
REDIS_PASSWORD=secure_redis_password
```

**Add this variable:**
```
APP_URL=https://yourdomain.com
```

### 5. Deploy (2 minutes)
```bash
# Push to trigger deployment
git add .
git commit -m "Setup image-only deployment"
git push origin main
```

**Watch deployment**: GitHub → Actions tab

## ✅ Verify Deployment

```bash
# Check containers
ssh your-user@your-server-ip "docker ps"

# Test application
curl http://your-server-ip/health
```

## 🎯 That's It!

Your NHIT application is now:
- ✅ **Fully automated** - Push code = Auto deploy
- ✅ **Secure** - No source code on server
- ✅ **Zero-downtime** - Rolling updates
- ✅ **Auto-rollback** - If deployment fails
- ✅ **Production-ready** - With monitoring & backups

## 🔧 Daily Commands

```bash
# Check status
ssh your-user@your-server-ip "cd /opt/nhit && docker-compose -f docker-compose.prod.yml ps"

# View logs
ssh your-user@your-server-ip "cd /opt/nhit && docker-compose -f docker-compose.prod.yml logs -f app"

# Manual deploy
ssh your-user@your-server-ip "cd /opt/nhit && ./deploy-image.sh ghcr.io/your-username/nhit:latest"
```

## 🆘 Need Help?

- **Full Guide**: See `IMAGE_ONLY_DEPLOYMENT_GUIDE.md`
- **Troubleshooting**: Check container logs and deployment logs
- **Health Check**: `curl http://your-server-ip/health`

---

**🎉 Your NHIT app is live with enterprise-grade deployment!**
