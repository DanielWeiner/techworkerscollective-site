# Tech Workers' Collective website

Google Cloud setup for the [Tech Workers' Collective website](https://www.techworkerscollective.org).

## Overview

The application runs on Cloud Run based on the base [WordPress Docker image](https://hub.docker.com/_/wordpress). It sits on top of a VPC and connects to an e2-micro instance running MariaDB. All secrets are configured in Secret Manager and provided as environment variables to the WordPress container. A Google Cloud Storage bucket is mounted to the container at /var/www/html/wp-content/uploads for uploaded media. The WordPress image is built locally and pushed to Artifact Registry.

### Wordpress image
The WordPress image is built from the official WordPress image. Any custom modifications to the WordPress files can be injected into the image through the `Dockerfile`. The built image is then pushed to Artifact Registry for use by Cloud Run.

The WordPress image assumes an immutable filesystem, and all uploaded media is stored in a Google Cloud Storage bucket mounted to the container at /var/www/html/wp-content/uploads. The bucket is configured to allow read/write access from the Cloud Run service. This means that any plugins or themes will need to be committed to the Docker image in order to persist across container restarts.

### Database
The database is hosted as a MariaDB instance on an e2-micro VM. The Cloud Run service points to the database via the WORDPRESS_DB_HOST environment variable.

## Deploy a new container image

1. Clone the repository:
   ```bash
  git clone https://github.com/DanielWeiner/techworkerscollective-site.git
  cd techworkerscollective-site
  ```
2. Build and push the WordPress image:
   ```bash
   cd docker
   docker compose build wordpress
   gcloud builds submit . --tag gcr.io/techworkerscollective-site/wordpress:latest
   ```
3. Deploy a new revision:
   ```bash
   gcloud run deploy SERVICE_NAME --region us-central1 --image gcr.io/techworkerscollective-site/wordpress:latest
   ```

## Local Development
For local development, you can use Docker Compose to run the WordPress container and a local MariaDB instance. Files and data are persisted to ./wp and ./db respectively. The WordPress container runs on port 8080.

```bash
cd docker
docker compose up -d
```

## License

This project is provided as-is. The WordPress Docker image is covered by the [WordPress license](https://github.com/WordPress/wordpress-develop/blob/trunk/GPL-2.0-or-later).
