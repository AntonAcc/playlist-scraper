FROM php:latest
COPY . /app
WORKDIR /app

RUN apt-get -qq update && apt-get -qq dist-upgrade
# Install zip-extension dependencies
RUN apt-get install -y libzip-dev
# Install zip-extension
RUN docker-php-ext-install zip
# Install chrome-php/chrome dependencies
RUN docker-php-ext-install sockets
# Install wget for getting Chrome package
RUN apt-get install -y wget
# Install Chrome dependencies
RUN apt-get install -y -qq --no-install-recommends fonts-liberation libasound2 libatk-bridge2.0-0 libatk1.0-0 \
    libatspi2.0-0 libcairo2 libcups2 libgbm1 libgtk-3-0 libnss3 libpango-1.0-0 libxkbcommon0 xdg-utils
# Install Chrome
RUN wget https://dl.google.com/linux/direct/google-chrome-stable_current_amd64.deb
RUN dpkg -i google-chrome-stable_current_amd64.deb
RUN rm google-chrome-stable_current_amd64.deb
# Install Composer dependencies
RUN apt-get install -y git
# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
# Install playlist-scraper dependencies
RUN composer install
