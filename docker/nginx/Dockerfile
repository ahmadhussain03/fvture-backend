# docker/nginx/Dockerfile
# Stage 1: Build assets
FROM debian AS builder

# Install Node.js and build tools
RUN apt-get update && apt-get install -y --no-install-recommends \
  curl \
  nodejs \
  npm \
  && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Set working directory
WORKDIR /var/www

# Copy Laravel application code
COPY . /var/www

# Install Node.js dependencies and build assets
RUN npm install && npm run build

# Stage 2: Nginx production image
FROM nginx:alpine

# Copy custom Nginx configuration
# -----------------------------------------------------------
# Replace the default Nginx configuration with our custom one
# that is optimized for serving a Laravel application.
# -----------------------------------------------------------
COPY ./docker/nginx/nginx.conf /etc/nginx/nginx.conf

# Copy Laravel's public assets from the builder stage
# -----------------------------------------------------------
# We only need the 'public' directory from our Laravel app.
# -----------------------------------------------------------
COPY --from=builder /var/www/public /var/www/public

# Copy Laravel storage directory for file uploads and logs
# -----------------------------------------------------------
# Copy the storage directory so the server can write to it
# -----------------------------------------------------------
COPY --from=builder /var/www/storage /var/www/storage

# Create necessary directories and set proper permissions
# -----------------------------------------------------------
# Ensure storage directories exist and have proper permissions
# for the web server to write files (logs, cache, sessions, etc.)
# -----------------------------------------------------------
RUN mkdir -p /var/www/storage/app/public \
    && mkdir -p /var/www/storage/app/private \
    && mkdir -p /var/www/storage/app/private/livewire-tmp \
    && mkdir -p /var/www/storage/framework/cache \
    && mkdir -p /var/www/storage/framework/sessions \
    && mkdir -p /var/www/storage/framework/views \
    && mkdir -p /var/www/storage/logs \
    && chown -R nginx:nginx /var/www/storage \
    && chmod -R 777 /var/www/storage

# Set the working directory to the public folder
WORKDIR /var/www/public

# Expose port 80 and start Nginx
EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]
