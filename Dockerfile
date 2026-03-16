# Two stage image build

# First stage - Setup PHP and NodeJs 
# Use serversideup's php image as the base image (update the tag to match the version you want to use)
FROM ghcr.io/serversideup/php:8.5-fpm-apache AS base

# Switch to root so we can do root things
USER root
 
# Install the ext-gd extension with root permissions (add any other extensions you need here)
RUN install-php-extensions intl gd exif imagick

# Install nvm and nodejs
RUN apt-get update && apt-get install -y curl
ENV NODE_VERSION=24.14.0
ENV NVM_DIR=/root/.nvm
RUN curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.0/install.sh | bash
RUN . "$NVM_DIR/nvm.sh" && nvm install ${NODE_VERSION}
RUN . "$NVM_DIR/nvm.sh" && nvm use v${NODE_VERSION}
RUN . "$NVM_DIR/nvm.sh" && nvm alias default v${NODE_VERSION}
ENV PATH="/root/.nvm/versions/node/v${NODE_VERSION}/bin/:${PATH}"

# Second stage - Setup App 
FROM base AS app

# Copy app file
COPY --chown=www-data:www-data . /var/www/html

# Setup app
RUN composer install --no-dev \ 
    && npm install \ 
    && npm run build \ 
    && rm -rf node_modules

# Drop back to our unprivileged user
USER www-data