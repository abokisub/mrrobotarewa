FROM serversideup/php:8.3-fpm-nginx

# Set the document root for Nginx to point to Laravel's public directory
ENV AUTORUN_ENABLED=true
ENV WEB_DOCUMENT_ROOT=/var/www/html/public

# Copy the application files
COPY --chown=webuser:webuser . .

# Run composer install to optimize and install dependencies
RUN composer install --no-interaction --optimize-autoloader --no-dev
