FROM php:8.4-fpm

WORKDIR /var/www/html/app

# Install system dependencies including Nginx
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libonig-dev \
    nginx \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    cron \
    supervisor \
    libx11-xcb1 \
    libxcomposite1 \
    libasound2 \
    libatk1.0-0 \
    libatk-bridge2.0-0 \
    libcairo2 \
    libcups2 \
    libdbus-1-3 \
    libexpat1 \
    libfontconfig1 \
    libgbm1 \
    libgcc1 \
    libglib2.0-0 \
    libgtk-3-0 \
    libnspr4 \
    libpango-1.0-0 \
    libpangocairo-1.0-0 \
    libstdc++6 \
    libx11-6 \
    libx11-xcb1 \
    libxcb1 \
    libxcomposite1 \
    libxcursor1 \
    libxdamage1 \
    libxext6 \
    libxfixes3 \
    libxi6 \
    libxrandr2 \
    libxrender1 \
    libxss1 \
    libxtst6

# Install Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash -
RUN apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_pgsql mbstring zip exif pcntl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Setup composer
COPY --from=composer /usr/bin/composer /usr/bin/composer

# Import project and install dependencies
COPY . /var/www/html/app
# RUN composer install --quiet --optimize-autoloader \
RUN composer install --quiet \
    && chown -R www-data:www-data /var/www/html/app \
    && chmod -R 775 /var/www/html/app/storage /var/www/html/app/bootstrap/cache \
    && chmod +x /var/www/html/app/entrypoint.sh

# Setup browsershot
RUN npm install \
    && npx puppeteer browsers install chrome-headless-shell \
    && npm run build

# Correct the cron job setup
RUN echo "* * * * * www-data cd /var/www/html/app && php artisan schedule:run >> /var/log/cron.log 2>&1" > /etc/cron.d/laravel-scheduler \
    && chmod 0644 /etc/cron.d/laravel-scheduler \
    && crontab -u www-data /etc/cron.d/laravel-scheduler

# Copy Supervisor configuration
COPY .docker/supervisord/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Ensure log directory exists for Supervisor, Laravel, and Nginx
RUN mkdir -p /var/log/supervisor /var/log/nginx && \
    touch /var/log/php-fpm.log /var/log/cron.log /var/log/laravel.log /var/log/laravel_worker.log && \
    chown -R www-data:www-data /var/log

# Copy your Nginx site configuration to replace the default
COPY .docker/nginx/default.conf /etc/nginx/sites-available/default

# Expose port 80 for Nginx
EXPOSE 80

# Start Supervisord
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]
