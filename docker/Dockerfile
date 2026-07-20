# Build a production-ready WordPress image for Cloud Run
# Based on the official WordPress Docker image

FROM wordpress:7.0.2-php8.3-apache

ENV WORDPRESS_DEBUG=0

RUN apt-get update && apt-get install -y \
    unzip \
    && rm -rf /var/lib/apt/lists/*

RUN mkdir -p /usr/src/wordpress-plugins
COPY ./plugins/ /usr/src/wordpress-plugins/
RUN mkdir -p /usr/src/wordpress/wp-content/plugins

RUN cd /usr/src/wordpress/wp-content/plugins && \
    find /usr/src/wordpress-plugins/ -name '*.zip' -exec unzip -o {} \; && \
    rm -rf /usr/src/wordpress-plugins

COPY ./src/ /usr/src/wordpress/

LABEL maintainer="Daniel Weiner <danielbweiner@gmail.com>"
LABEL stage="cloudrun"
LABEL description="WordPress for Cloud Run - built from official WordPress Docker image"
