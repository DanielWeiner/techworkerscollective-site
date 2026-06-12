# Tech Workers' Collective website

Google Cloud setup for the [Tech Workers' Collective website](https://www.techworkerscollective.org).

## Overview

The application runs on Cloud Run based on the base [WordPress Docker image](https://hub.docker.com/_/wordpress). It sits on top of a VPC and connects to two e2-micro instances: one running MariaDB and the serving NFS. All secrets are configured in Secret Manager and provided as environment variables to the 
WordPress container. The WordPress image is built locally and pushed to Artifact Registry.

### Wordpress image
The WordPress image is built from the official WordPress image. Any custom modifications to the WordPress files can be injected into the image through the `Dockerfile`. The built image is then pushed to Artifact Registry for use by Cloud Run.

Upon first run, the Wordpress container will copy files from /usr/src/wordpress to /var/www/html. Because /var/www/html is mounted as an NFS volume, these files persist across container restarts.

### Database and NFS
The database is hosted as a MariaDB instance on an e2-micro VM. The Cloud Run service points to the database via the WORDPRESS_DB_HOST environment variable. The NFS is also hosted on an e2-micro VM and is mounted to the WordPress container as a volume. The NFS server serves `/share/html`, which is mounted to `/var/www/html` in the WordPress container.

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
3. Update the terraform:
  ```
  ...
  resource resource "google_cloud_run_v2_service" "wordpress" {
    ...
    template {
      ...
      containers {
        image = "gcr.io/techworkerscollective-site/wordpress@sha256:<latest-sha256>"
      }
    }
  }
  ...
  ```
4. Deploy the infrastructure:
  ```bash
  terraform init
  terraform apply
  ```

## Local Development
For local development, you can use Docker Compose to run the WordPress container and a local MariaDB instance. Files and data are persisted to ./wp and ./db respectively. The WordPress container runs on port 8080.

```bash
cd docker
docker compose up -d
```

## License

This project is provided as-is. The WordPress Docker image is covered by the [WordPress license](https://github.com/WordPress/wordpress-develop/blob/trunk/GPL-2.0-or-later).
