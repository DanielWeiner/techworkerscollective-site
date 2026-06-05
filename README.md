# Tech Workers' Collective - WordPress on Google Cloud

A bare-bones Terraform setup for hosting WordPress on Google Cloud Platform.

## What's Included

- **Cloud SQL MySQL 8.0** - Managed database with automatic backups
- **Cloud Run** - Serverless environment for WordPress
- **Environment variables** - Pre-configured database connection details

## Quick Start

### 1. Initialize Terraform

```bash
terraform init
```

### 2. Configure Variables

Edit `variables.tf` or create a `terraform.tfvars` file with your values:

```hcl
project_id = "your-gcp-project-id"
region     = "us-central1"
database_name = "wordpress"
database_user = "wordpress_user"
database_password = "your-secure-password"
```

**Important:** Never commit `terraform.tfvars` or `.env` files to version control.

### 3. Validate Configuration

```bash
terraform validate
```

### 4. Plan the Deployment

```bash
terraform plan
```

### 5. Deploy (when ready)

```bash
terraform apply
```

## Configuration Details

### Cloud SQL
- **Database Version:** MySQL 8.0
- **Tier:** e0 (Shared-core)
- **Backups:** Enabled at 3:00 AM UTC
- **Network:** Currently unrestricted (0.0.0.0/0) - restrict this in production!

### Cloud Run
- **Memory:** 2GB
- **CPU:** 1 vCPU
- **WordPress Version:** Latest (use specific versions for production)
- **Autoscaling:** 1-10 instances

## Security Checklist

- [ ] Change default passwords
- [ ] Restrict Cloud SQL authorized networks (remove 0.0.0.0/0)
- [ ] Use a specific WordPress version instead of `latest`
- [ ] Enable Cloud SQL IP whitelist
- [ ] Set up Cloud Run security policies
- [ ] Enable VPC Access instead of public IP

## Next Steps

1. **Add WordPress Configuration:** Create additional Terraform resources for WordPress configuration files
2. **Set up SSL:** Configure SSL certificates with Google Cloud Load Balancing or cert-manager
3. **Add Media Storage:** Set up Cloud Storage bucket for uploads
4. **Implement CI/CD:** Use Terraform Cloud or GitHub Actions for deployments
5. **Monitoring:** Set up Cloud Monitoring and Alerting

## Useful Commands

```bash
# List resources
terraform output

# Destroy everything
terraform destroy

# Show the execution plan
terraform plan -out=tfplan

# Apply the plan
terraform apply tfplan
```

## Resources

- [Terraform Google Provider Documentation](https://registry.terraform.io/providers/hashicorp/google/latest/docs)
- [Cloud SQL for MySQL Documentation](https://cloud.google.com/sql/docs/mysql)
- [Cloud Run Documentation](https://cloud.google.com/run/docs)
- [WordPress Docker Image](https://hub.docker.com/_/wordpress)

---

Built for the Tech Workers' Collective
