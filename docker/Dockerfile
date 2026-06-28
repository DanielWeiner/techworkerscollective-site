# Build a production-ready WordPress image for Cloud Run
# Based on the official WordPress Docker image

FROM wordpress:7.0.0-php8.3-apache

ENV WORDPRESS_DEBUG=0

RUN mkdir -p /usr/src/wordpress-custom && \
    chown www-data:www-data /usr/src/wordpress-custom

COPY ./src/ /usr/src/wordpress-custom/

COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

LABEL maintainer="Daniel Weiner <danielbweiner@gmail.com>"
LABEL stage="cloudrun"
LABEL description="WordPress for Cloud Run - built from official WordPress Docker image"
