# WordPress for Cloud Run

Builds a production-ready WordPress image based on the official [WordPress Docker image](https://hub.docker.com/_/wordpress) for deployment on Google Cloud Run with Artifact Registry.

## Quick Start

```bash
# Replace PROJECT_ID with your GCP project ID
PROJECT_ID="your-gcp-project-id"

# Build and push to Artifact Registry
gcloud builds submit . \
  --tag gcr.io/$PROJECT_ID/wordpress:latest \
  --tag gcr.io/$PROJECT_ID/wordpress:$(date +%Y%m%d)
```

## Configuration

### Database Settings

Set the following build arguments in `compose.yaml` or pass them to `gcloud builds submit`:

| Variable | Description | Default |
|----------|-------------|---------|
| `DB_HOST` | MySQL/MariaDB service name | `mysql` |
| `DB_USER` | Database user | `wordpress` |
| `DB_PASSWORD` | Database password | `wordpress` (change in production!) |
| `DB_NAME` | Database name | `wordpress` |
| `TABLE_PREFIX` | WordPress table prefix | `wp_` |

### Authentication Keys (Optional but Recommended)

Set these environment variables in Cloud Run deployment:

```bash
gcloud run deploy wordpress \
  --image gcr.io/$PROJECT_ID/wordpress:latest \
  --set-env-vars WORDPRESS_AUTH_KEY=your-auth-key \
  --set-env-vars WORDPRESS_SECURE_AUTH_KEY=your-secure-auth-key \
  --set-env-vars WORDPRESS_LOGGED_IN_KEY=your-logged-in-key \
  --set-env-vars WORDPRESS_NONCE_KEY=your-nonce-key
```

Generate secure keys using:
```bash
echo '$$rp(32)|$$rp(32)|$$rp(32)|$$rp(32)|$$rp(32)' | base64 -w0
```

## Deployment to Cloud Run

```bash
gcloud run deploy wordpress \
  --image gcr.io/$PROJECT_ID/wordpress:latest \
  --platform managed \
  --region us-central1 \
  --memory 2Gi \
  --cpu 2 \
  --min-instances 1 \
  --max-instances 10 \
  --allow-unauthenticated
```

### Required Environment Variables

| Variable | Description | Required |
|----------|-------------|----------|
| `WORDPRESS_DB_HOST` | Database hostname | Yes |
| `WORDPRESS_DB_USER` | Database user | Yes |
| `WORDPRESS_DB_PASSWORD` | Database password | Yes |
| `WORDPRESS_DB_NAME` | Database name | Yes |
| `WORDPRESS_TABLE_PREFIX` | Table prefix | Optional |

### Example: Cloud Run with Cloud SQL

```bash
# Get Cloud SQL proxy image
gcloud sql connections create wordpress-proxy --user=YOUR_SA \
  --database=wordpress --ip-range=0.0.0.0/0

# Deploy with Cloud SQL proxy
gcloud run deploy wordpress \
  --image gcr.io/$PROJECT_ID/wordpress:latest \
  --set-env-vars WORDPRESS_DB_HOST=127.0.0.1 \
  --set-env-vars WORDPRESS_DB_USER=wordpress \
  --set-env-vars WORDPRESS_DB_PASSWORD=your-password \
  --set-env-vars WORDPRESS_DB_NAME=wordpress \
  --add-cloudsql-instances=PROJECT_ID:REGION:instance_name
```

## Production Best Practices

### 1. Use Read-Only Filesystem

For maximum security, run WordPress with a read-only root filesystem:

```bash
gcloud run deploy wordpress \
  --image gcr.io/$PROJECT_ID/wordpress:latest \
  --add-volume wp-content/uploads \
  --volume-size 10Gi \
  --add-volume-source gcs \
  --bucket wp-uploads-bucket
```

Or use tmpfs mounts via custom container configuration.

### 2. Regular Rebuilds

Rebuild and redeploy at least weekly to get the latest WordPress security updates:

```bash
# Automated rebuild on schedule
# Add to crontab or use Cloud Build triggers
gcloud builds submit . --tag gcr.io/$PROJECT_ID/wordpress:latest
```

### 3. Health Checks

Add a health check to your Cloud Run deployment:

```bash
gcloud run deploy wordpress \
  --image gcr.io/$PROJECT_ID/wordpress:latest \
  --set-env-vars SERVER_NAME=0.0.0.0 \
  --set-env-vars PORT=80 \
  --add-cloudrun-natural-health-check
```

### 4. SSL/TLS

Cloud Run automatically provides SSL certificates for your custom domain.

## Local Development

### Build Locally

```bash
docker build -t wordpress:local .
docker run -p 8080:80 wordpress:local
```

### Development with Docker Compose

```bash
docker-compose up -d
```

## Troubleshooting

### WordPress can't connect to database

- Verify `DB_HOST` matches your MySQL service name
- Ensure the database already exists (WordPress won't create it)
- Check that `DB_USER` and `DB_PASSWORD` are correct

### WordPress can't write to uploads directory

- The image doesn't include PHP mail() support - configure SMTP
- For read-only deployments, ensure uploads are mounted to a writable location
- Set proper permissions on the uploads directory

### WordPress debug mode is enabled

- Ensure `WORDPRESS_DEBUG=0` (or unset) in production
- Check for any `WORDPRESS_DEBUG=1` environment variables

### Image is not up to date

- Rebuild the image - WordPress updates are included in new image builds
- The `latest` tag will always contain the newest WordPress version

## Security Considerations

⚠️ **Important Security Notes from Official WordPress Docker Image**:

1. **No additional PHP extensions**: The image doesn't provide email capabilities or other common PHP extensions. Configure SMTP for email functionality.

2. **Read-only deployments**: For production, run the image read-only with tmpfs mounts for:
   - `/tmp` (required by WordPress)
   - `/run` (required by PHP)
   - `wp-content/uploads` (optional, for uploads)

3. **Regular updates**: Rebuild and redeploy frequently to get security patches. Don't rely on WordPress's automatic updates.

4. **Authentication keys**: Generate and set secure authentication keys to prevent security vulnerabilities.

5. **Debug mode**: Never enable `WORDPRESS_DEBUG=1` in production.

## References

- [Official WordPress Docker Image](https://hub.docker.com/_/wordpress)
- [Cloud Run Documentation](https://cloud.google.com/run/docs)
- [Artifact Registry Documentation](https://cloud.google.com/artifact-registry)
- [WordPress Security Best Practices](https://wordpress.org/security/)

## License

This project is provided as-is. The WordPress Docker image is covered by the [WordPress license](https://github.com/WordPress/wordpress-develop/blob/trunk/GPL-2.0-or-later).
