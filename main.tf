provider "google" {
  project = "techworkerscollective-site"
  region  = "us-central1"
}

import {
  for_each = toset(local.secrets)
  id       = each.key
  to       = google_secret_manager_secret.wp_secrets[each.key]
}

import {
  id = "projects/techworkerscollective-site/locations/us-central1/services/techworkerscollective"
  to = google_cloud_run_v2_service.wordpress
}
import {
  id = "projects/techworkerscollective-site/zones/us-central1-f/instances/techworkerscollective-site-db"
  to = google_compute_instance.db
}
import {
  id = "projects/techworkerscollective-site/zones/us-central1-f/instances/techworkerscollective-site-nfs"
  to = google_compute_instance.nfs
}
import {
  id = "projects/techworkerscollective-site/global/networks/techworkerscollective-site-vpc"
  to = google_compute_network.vpc
}
import {
  id = "projects/techworkerscollective-site/regions/us-central1/subnetworks/techworkerscollective-site-subnet"
  to = google_compute_subnetwork.subnet
}

# --- VPC & Networking ---

resource "google_compute_network" "vpc" {
  name                    = "techworkerscollective-site-vpc"
  auto_create_subnetworks = false
}

resource "google_compute_subnetwork" "subnet" {
  name                     = "techworkerscollective-site-subnet"
  ip_cidr_range            = "10.0.0.0/26"
  region                   = "us-central1"
  network                  = google_compute_network.vpc.id
  private_ip_google_access = true
}

# --- Service Account ---

data "google_service_account" "sa" {
  account_id = "techworkerscollective-sa"
}

# --- Compute Instances ---

resource "google_compute_instance" "db" {
  name         = "techworkerscollective-site-db"
  machine_type = "e2-micro"
  zone         = "us-central1-f"
  key_revocation_action_type = "NONE"
  allow_stopping_for_update = true
  boot_disk {
    initialize_params {
      image = "https://www.googleapis.com/compute/v1/projects/debian-cloud/global/images/debian-13-trixie-v20260528"
      size  = 20
    }
  }

  network_interface {
    network    = google_compute_network.vpc.name
    subnetwork = google_compute_subnetwork.subnet.name
    network_ip = "10.0.0.3"

    access_config {
      network_tier = "STANDARD"
    }
  }

  service_account {
    email  = data.google_service_account.sa.email
    scopes = ["cloud-platform"]
  }
}

resource "google_compute_instance" "nfs" {
  name         = "techworkerscollective-site-nfs"
  machine_type = "e2-micro"
  zone         = "us-central1-f"
  key_revocation_action_type = "NONE"
  allow_stopping_for_update = true
  boot_disk {
    initialize_params {
      image = "https://www.googleapis.com/compute/v1/projects/debian-cloud/global/images/debian-12-bookworm-v20260528"
      size  = 10
    }
  }

  network_interface {
    network    = google_compute_network.vpc.name
    subnetwork = google_compute_subnetwork.subnet.name
    network_ip = "10.0.0.4"

    access_config {
      network_tier = "STANDARD"
    }
  }

  service_account {
    email  = data.google_service_account.sa.email
    scopes = ["cloud-platform"]
  }
}

# --- Secret Manager Secrets ---

locals {
  secrets = [
    "WORDPRESS_AUTH_KEY",
    "WORDPRESS_AUTH_SALT",
    "WORDPRESS_DB_NAME",
    "WORDPRESS_DB_PASSWORD",
    "WORDPRESS_DB_USER",
    "WORDPRESS_LOGGED_IN_KEY",
    "WORDPRESS_LOGGED_IN_SALT",
    "WORDPRESS_NONCE_KEY",
    "WORDPRESS_NONCE_SALT",
    "WORDPRESS_SECURE_AUTH_KEY",
    "WORDPRESS_SECURE_AUTH_SALT"
  ]
}



resource "google_secret_manager_secret" "wp_secrets" {
  for_each  = toset(local.secrets)
  secret_id = each.key

  replication {
    auto {}
  }
}



# --- Cloud Run v2 Service ---

resource "google_cloud_run_v2_service" "wordpress" {
  name     = "techworkerscollective"
  location = "us-central1"
  ingress  = "INGRESS_TRAFFIC_ALL"
  invoker_iam_disabled  = true

  template {
    service_account = data.google_service_account.sa.email
    timeout         = "300s"
    max_instance_request_concurrency = 80

    scaling {
      min_instance_count = 1
      max_instance_count = 20
    }

    containers {
      image = "gcr.io/techworkerscollective-site/wordpress@sha256:2b5b560830cc01c5e40e29de7a1896043efdb48e9d4a6b4d73431e43820de872"

      resources {
        limits = {
          cpu    = "1000m"
          memory = "512Mi"
        }
      }

      ports {
        container_port = 80
      }

      env {
        name  = "WORDPRESS_DB_HOST"
        value = "10.0.0.3"
      }

      env {
        name  = "WORDPRESS_DEBUG"
        value = "0"
      }

      # Dynamically map the discovered secret definitions
      dynamic "env" {
        for_each = local.secrets
        content {
          name = env.value
          value_source {
            secret_key_ref {
              secret  = env.value
              version = "latest"
            }
          }
        }
      }

      volume_mounts {
        name       = "nfs-1"
        mount_path = "/var/www/html"
      }

      startup_probe {
        timeout_seconds   = 20
        period_seconds    = 20
        failure_threshold = 20
        tcp_socket {
          port = 80
        }
      }
    }

    # Direct VPC egress setup
    vpc_access {
      network_interfaces {
        network    = google_compute_network.vpc.id
        subnetwork = google_compute_subnetwork.subnet.id
      }
      egress = "PRIVATE_RANGES_ONLY"
    }

    volumes {
      name = "nfs-1"
      nfs {
        server = "10.0.0.4"
        path   = "/share/html"
      }
    }
  }


  # Ensure secrets exist before service deployment
  depends_on = [google_secret_manager_secret.wp_secrets]
}
