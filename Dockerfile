FROM serversideup/php:8.4-fpm-nginx

# Switch to root to configure permissions
USER root

# Set the document root for Nginx to point to Laravel's public directory
ENV AUTORUN_ENABLED=true
ENV WEB_DOCUMENT_ROOT=/var/www/html/public

# Copy the application files with correct ownership
COPY --chown=webuser:webuser . .

# Ensure all Laravel storage and cache directories exist and are writable
RUN mkdir -p storage/framework/cache/data \
             storage/framework/sessions \
             storage/framework/views \
             storage/logs \
             bootstrap/cache && \
    chown -R webuser:webuser storage bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache

# Switch back to webuser to run composer install securely
USER webuser

# Run composer install to optimize and install dependencies
RUN composer install --no-interaction --optimize-autoloader --no-dev

