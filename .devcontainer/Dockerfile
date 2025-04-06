FROM php:8.4-fpm

WORKDIR /var/www/html/app

# Setup basic dependencies
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libonig-dev \
    curl \
    libxml2-dev \
    libpng-dev \
    zip \
    unzip \
    libzip-dev \
    git

# Install Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash -
RUN apt-get install -y nodejs

# Setup php extensions
RUN docker-php-ext-install pdo pdo_pgsql mbstring zip exif pcntl bcmath

# Setup redis
RUN pecl install -o -f redis \
    && rm -rf /tmp/pear \
    && docker-php-ext-enable redis

# Setup XDebug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Setup composer
COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN apt-get update && apt-get install -y \
    libnss3 \
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

# Setup permissions
ARG USERNAME=dev
ARG UID=1000
ARG GID=1000

# Using Ubuntu's groupadd and useradd commands
RUN groupadd -g ${GID} ${USERNAME} && \
    useradd -u ${UID} -g ${GID} -s /bin/sh -m ${USERNAME}

USER ${USERNAME}

# Expose port
EXPOSE 9000

# Start PHP-FPM
CMD ["php-fpm"]
