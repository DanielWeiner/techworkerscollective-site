# Tech Workers' Collective - WordPress Variables

# Project and Region
variable "project_id" {
  description = "GCP Project ID"
  type        = string
  default     = "your-gcp-project-id"
}

variable "region" {
  description = "GCP Region (e.g., us-central1, europe-west1)"
  type        = string
  default     = "us-central1"
}

# Database Configuration
variable "database_name" {
  description = "Cloud SQL Database name"
  type        = string
  default     = "wordpress"
}

variable "database_user" {
  description = "Cloud SQL Database user for WordPress"
  type        = string
  default     = "wordpress_user"
}

# Sensitive variables - NEVER commit passwords to version control
variable "database_password" {
  description = "Cloud SQL Database password (use env var GOOGLE_CLOUD_DATABASE_PASSWORD)"
  type        = string
  sensitive   = true
  default     = ""
}

# Cloud SQL Instance
variable "database_instance_name" {
  description = "Cloud SQL instance name"
  type        = string
  default     = "techworkers-wordpress"
}

# Cloud Run Configuration
variable "run_service_id" {
  description = "Cloud Run service name"
  type        = string
  default     = "wordpress"
}

variable "run_image" {
  description = "WordPress image (can use specific version like wordpress:6.4)"
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
