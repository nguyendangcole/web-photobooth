# 🚀 CI/CD Setup Instructions

## 📋 Prerequisites

1. **GitHub Repository** with enabled Actions
2. **Docker Hub** or **GitHub Container Registry** access
3. **Hostinger FTP** credentials for deployment

## 🔧 Setup Steps

### 1. Configure GitHub Secrets

Go to your GitHub repository → Settings → Secrets and variables → Actions

Add these secrets:

```bash
# Deployment
FTP_SERVER=your-hostinger-ftp-server.com
FTP_USERNAME=your-ftp-username
FTP_PASSWORD=your-ftp-password

# Notifications (optional)
SLACK_WEBHOOK=https://hooks.slack.com/services/...
```

### 2. Enable Container Registry

```bash
# In your GitHub repo settings
# Settings → Actions → General → Workflow permissions
# ✅ Read and write permissions
# ✅ Allow GitHub Actions to create and approve pull requests
```

### 3. Local Testing

```bash
# Install dependencies
composer install

# Run tests
composer test

# Run linting
composer lint

# Run static analysis
composer analyse

# Docker build test
docker build -t photobooth-test .
```

### 4. Docker Compose Development

```bash
# Start development environment
docker-compose up -d

# Access services
# App: http://localhost:8080
# PhpMyAdmin: http://localhost:8081
# MailHog: http://localhost:8025

# Stop services
docker-compose down
```

## 🔄 Pipeline Workflow

### On Push to `main`:
1. ✅ Setup & Dependencies
2. ✅ Quality Checks (Syntax, Style, Analysis)
3. ✅ Security Scan (Trivy)
4. ✅ Testing (PHPUnit)
5. ✅ Docker Build & Push
6. ✅ Deploy to Hostinger
7. ✅ Slack Notification

### On Pull Request:
1. ✅ Setup & Dependencies
2. ✅ Quality Checks
3. ✅ Security Scan
4. ✅ Testing

## 📊 Quality Gates

### Code Quality
- ✅ PHP Syntax Check
- ✅ PSR-12 Code Style
- ✅ PHPStan Level 5 Analysis

### Security
- ✅ Trivy Vulnerability Scan
- ✅ SARIF Results Upload

### Testing
- ✅ PHPUnit Test Suite
- ✅ Code Coverage (80%+ target)

## 🐳 Docker Images

### Production Build
```bash
# Build production image
docker build --target production -t photobooth:prod .

# Run production container
docker run -p 80:80 photobooth:prod
```

### Development Build
```bash
# Build development image
docker build --target development -t photobooth:dev .

# Run with volume mounts
docker run -p 8080:80 -v .:/var/www/html photobooth:dev
```

## 🚀 Deployment

### Automatic Deployment
- Triggered on push to `main` branch
- FTP deployment to Hostinger
- Production environment variables

### Manual Deployment
```bash
# Deploy manually
gh workflow run ci-cd.yml

# Check deployment status
gh run list
```

## 🔍 Monitoring

### Pipeline Status
- GitHub Actions tab
- Real-time build logs
- Artifact downloads

### Security Dashboard
- GitHub Security tab
- Vulnerability reports
- SARIF results

### Code Coverage
- Codecov integration
- Coverage trends
- Test metrics

## 🛠️ Troubleshooting

### Common Issues

**Build Failures**
```bash
# Check dependencies
composer install --no-dev

# Verify PHP version
php --version

# Check extensions
php -m | grep -E "(pdo|gd|curl)"
```

**Test Failures**
```bash
# Run specific test
vendor/bin/phpunit tests/Unit/AuthTest.php

# Debug with coverage
vendor/bin/phpunit --coverage-html coverage
```

**Docker Issues**
```bash
# Clean build
docker-compose down --volumes
docker system prune -f

# Rebuild containers
docker-compose build --no-cache
```

**Deployment Issues**
```bash
# Test FTP connection
ftp $FTP_SERVER

# Check file permissions
ls -la public/

# Verify .env file
cat .env
```

## 📈 Optimization

### Build Speed
- ✅ Composer dependency caching
- ✅ Docker layer caching
- ✅ Parallel job execution

### Security
- ✅ Automated vulnerability scanning
- ✅ Dependency updates
- ✅ Secret management

### Performance
- ✅ Multi-platform builds
- ✅ Optimized Docker images
- ✅ CDN deployment

## 📚 Resources

- [GitHub Actions Documentation](https://docs.github.com/en/actions)
- [Docker Best Practices](https://docs.docker.com/develop/dev-best-practices/)
- [PHPUnit Testing](https://phpunit.de/documentation.html)
- [PHPStan Static Analysis](https://phpstan.org/)
- [Trivy Security Scanner](https://github.com/aquasecurity/trivy)

## 🆘 Support

For issues:
1. Check GitHub Actions logs
2. Review Docker container logs
3. Verify environment configuration
4. Test locally before pushing

---

**🚀 Ready to automate your deployment workflow!**
