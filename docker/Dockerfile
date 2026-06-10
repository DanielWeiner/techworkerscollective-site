# Build a production-ready WordPress image for Cloud Run
# Based on the official WordPress Docker image

FROM wordpress:7.0.0-php8.3-apache

ENV WORDPRESS_DEBUG=0

COPY wp-config.php /usr/src/wordpress/wp-config.php
COPY .htaccess /usr/src/wordpress/.htaccess

LABEL maintainer="Daniel Weiner <danielbweiner@gmail.com>"
LABEL stage="cloudrun"
LABEL description="WordPress for Cloud Run - built from official WordPress Docker image"
