# Tech Workers' Collective - WordPress on Google Cloud
# Terraform configuration

terraform {
  required_version = ">= 1.0.0"
  required_providers {
    google = {
      source  = "hashicorp/google"
      version = "5.0.0"
    }
  }
}

# Provider configuration
provider "google" {
  project = var.project_id
  region  = var.region
}

# Variables
variable "project_id" {
  description = "GCP Project ID"
  type        = string
  default     = "your-gcp-project-id"
}

variable "region" {
  description = "GCP Region"
  type        = string
  default     = "us-central1"
}

variable "database_name" {
  description = "Cloud SQL Database name"
  type        = string
  default     = "wordpress"
}

variable "database_user" {
  description = "Cloud SQL Database user"
  type        = string
  default     = "wordpress_user"
}

variable "database_password" {
  description = "Cloud SQL Database password"
  type        = string
  sensitive   = true
  default     = ""
}

variable "database_instance_name" {
  description = "Cloud SQL instance name"
  type        = string
  default     = "techworkers-wordpress"
}

variable "run_service_id" {
  description = "Cloud Run service name"
  type        = string
  default     = "wordpress"
}

variable "run_image" {
  description = "WordPress image URL"
  type        = string
  default     = "wordpress:latest"
}

variable "run_memory_gb" {
  description = "Cloud Run memory allocation (GB)"
  type        = number
  default     = 2
}

variable "run_cpu" {
  description = "Cloud Run CPU allocation"
  type        = string
  default     = "1"
}

# Cloud SQL MySQL Database
resource "google_sql_database" "wordpress" {
  name     = var.database_name
  instance = google_sql_database_instance.this.name
}

# Cloud SQL Database Instance
resource "google_sql_database_instance" "this" {
  name             = var.database_instance_name
  database_version = "MYSQL_8_0"
  deletion_protection = false

  settings {
    tier              = "e0"
    backup_configuration {
      enabled  = true
      start_time = "03:00:00"
    }
    ip_configuration {
      ipv4_enabled    = true
      authorized_networks {
        name  = "unrestricted"
        value = "0.0.0.0/0"
      }
    }
  }

  dependencies = [google_sql_database.this]
}

# Cloud Run Service
resource "google_cloud_run_v2_service" "wordpress" {
  name     = var.run_service_id
  location = var.region
  ingress  = "INGRESS_TRAFFIC_ALL"

  template {
    spec {
      containers {
        image = var.run_image

        resources {
          limits_cpu    = var.run_cpu
          limits_memory = "${var.run_memory_gb}Gi"
        }

        env {
          name  = "WORDPRESS_DB_HOST"
          value = google_sql_database_instance.this.connect_details.connection_name
        }

        env {
          name  = "WORDPRESS_DB_USER"
          value = var.database_user
        }

        env {
          name  = "WORDPRESS_DB_PASSWORD"
          value = var.database_password
        }

        env {
          name  = "WORDPRESS_DB_NAME"
          value = var.database_name
        }
      }
    }

    metadata {
      annotations = {
        run.googleapis.com/autoscaling-max = "10"
        run.googleapis.com/autoscaling-min = "1"
      }
    }
  }

  traffic {
    percent  = 100
    location = var.region
  }
}

# Output values
output "database_connection_name" {
  description = "Cloud SQL database connection name"
  value       = google_sql_database_instance.this.connect_details.connection_name
  sensitive   = true
}

output "run_url" {
  description = "Cloud Run WordPress service URL"
  value       = google_cloud_run_v2_service.this.status[0].url
}
