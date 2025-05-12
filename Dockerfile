# Adapted from https://github.com/tofran/providence-docker/blob/main/Dockerfile
# https://camanual.whirl-i-gig.com/providence/user/setup/systemReq
FROM php:8.3-apache-bookworm

# Set correct timezone
RUN ln -sf /usr/share/zoneinfo/America/Los_Angeles /etc/localtime

# Install libraries needed for PHP extensions CollectiveAccess requires.
RUN apt-get update && \
    apt-get upgrade -y && \
    apt-get install --no-install-recommends -y \
        libgmp-dev libicu-dev libpng-dev libzip-dev

# mysqlnd (native driver) is already installed; mysqli appears to also be needed,
# but not pdo_mysql.
# Install and enable necessary PHP extensions, the php-in-docker way.
RUN docker-php-ext-install bcmath gd gmp intl mysqli opcache zip && \
    docker-php-ext-enable bcmath gd gmp intl mysqli opcache zip
# TODO: configure / enable opcache, see https://laravel-news.com/php-opcache-docker

# Install imagemagick, poppler-utils, and ghostscript, all of which apparently are needed for PDF
# support.  # poppler-utils provides pdf info but not viewing.
RUN apt-get install --no-install-recommends -y graphicsmagick ghostscript poppler-utils

# The application will be placed here, apache's default directory in this base image.
WORKDIR /var/www/html

# Create a new user for running apache, to avoid conflicts when running via
# docker in development environment.
# Variables supplied via docker compose.
ARG APACHE_RUN_USER
ARG APACHE_RUN_GROUP

RUN useradd -c "Special apache app user" -d /var/www --shell=/usr/sbin/nologin "$APACHE_RUN_USER"

RUN chown "$APACHE_RUN_USER":"$APACHE_RUN_USER" /var/www/html

# Switch to the custom apache user
USER "$APACHE_RUN_USER"

# Copy application files to image, ensuring ownership and permissions for apache.
# This copies only our pre-built set of application code, not supporting files
# only needed for building or deploying.
COPY --chown="$APACHE_RUN_USER":"$APACHE_RUN_USER" ./ucla_providence /var/www/html

# Expose the default HTTP port.
EXPOSE 80

# No explicit ENTRYPOINT or CMD; the base container runs
# /usr/local/bin/docker-php-entrypoint which runs apache2-foreground.
