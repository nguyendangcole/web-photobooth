# 🚀 CI/CD Pipeline Documentation

## Overview

This repository implements a comprehensive CI/CD pipeline using GitHub Actions for the Space Photobooth application.

## 🔄 Workflow Triggers

The pipeline automatically runs on:
- **Push** to `main` or `develop` branches
- **Pull Requests** to `main` branch

## 📋 Pipeline Stages

### 1. 📦 Setup Environment
- Sets up PHP 8.3 with required extensions
- Caches Composer dependencies for faster builds
- Installs all PHP dependencies

### 2. 🔍 Quality Checks
- **PHP Syntax Validation**: Checks all PHP files for syntax errors
- **Code Style**: PHP-CS-Fixer for consistent code formatting
- **Static Analysis**: PHPStan for code quality and potential issues

### 3. 🐳 Docker Build (Main Branch Only)
- Multi-platform builds (AMD64 & ARM64)
- Pushes to GitHub Container Registry
- Automatic tagging based on branch and commit

### 4. 🔒 Security Scanning
- Trivy vulnerability scanner
- SARIF format results uploaded to GitHub Security tab

### 5. 🧪 Testing
- MySQL 8.0 test database setup
- PHPUnit test execution
- Code coverage reporting to Codecov

### 6. 🚀 Deployment (Main Branch Only)
- FTP deployment to Hostinger
- Slack notifications for deployment status

## 🔧 Required Secrets

Configure these secrets in your GitHub repository:

### Docker & Registry
- `GITHUB_TOKEN`: Automatically provided by GitHub Actions

### Deployment
- `FTP_SERVER`: Your Hostinger FTP server
- `FTP_USERNAME`: FTP username
- `FTP_PASSWORD`: FTP password

### Notifications
- `SLACK_WEBHOOK`: Slack webhook URL for deployment notifications

## 🐳 Docker Images

### Production Image
```bash
docker pull ghcr.io/nguyendangcole/web-photobooth:latest
```

### Development Image
```bash
docker pull ghcr.io/nguyendangcole/web-photobooth:develop
```

## 🏃‍♂️ Local Development

### Using Docker Compose
```bash
# Start all services
docker-compose up -d

# View logs
docker-compose logs -f app

# Stop services
docker-compose down
```

### Access Points
- **Application**: http://localhost:8080
- **PhpMyAdmin**: http://localhost:8081
- **MailHog**: http://localhost:8025
- **MySQL**: localhost:3306
- **Redis**: localhost:6379

## 📊 Quality Metrics

### Code Coverage
- Target: 80%+ coverage
- Automated reporting to Codecov

### Code Style
- PSR-12 compliance
- Automated formatting with PHP-CS-Fixer

### Static Analysis
- PHPStan Level 5
- Zero high-impact issues allowed

## 🔍 Monitoring

### Pipeline Status
- GitHub Actions tab shows real-time status
- All stages must pass for deployment

### Security
- Automated vulnerability scanning
- SARIF results in Security tab

### Performance
- Build time optimization with caching
- Parallel job execution

## 🚀 Deployment Process

1. **Code Push** → Triggers pipeline
2. **Quality Checks** → Validates code quality
3. **Security Scan** → Ensures security
4. **Testing** → Validates functionality
5. **Docker Build** → Creates container image
6. **Deployment** → Pushes to production
7. **Notification** → Updates team

## 🛠️ Customization

### Adding New Tests
1. Create test files in `tests/` directory
2. Follow naming convention: `*Test.php`
3. Run `composer test` locally

### Modifying Pipeline
1. Edit `.github/workflows/ci-cd.yml`
2. Test changes in a feature branch
3. Submit pull request for review

### Docker Configuration
1. Modify `Dockerfile` for build changes
2. Update `docker-compose.yml` for local development
3. Test with `docker-compose build`

## 📝 Best Practices

- ✅ Write descriptive commit messages
- ✅ Include tests with new features
- ✅ Follow PSR-12 coding standards
- ✅ Update documentation
- ✅ Test locally before pushing

## 🆘 Troubleshooting

### Common Issues
- **Build Failures**: Check dependency versions
- **Test Failures**: Verify database setup
- **Deployment Issues**: Check FTP credentials

### Debug Commands
```bash
# Check pipeline logs
gh run list

# View specific run
gh run view <run-id>

# Debug locally
docker-compose logs app
```

## 📚 Additional Resources

- [GitHub Actions Documentation](https://docs.github.com/en/actions)
- [Docker Best Practices](https://docs.docker.com/develop/dev-best-practices/)
- [PHPUnit Testing](https://phpunit.de/documentation.html)
- [PHPStan Static Analysis](https://phpstan.org/)
