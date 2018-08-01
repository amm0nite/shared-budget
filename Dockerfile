FROM ubuntu:18.04

ENV DEBIAN_FRONTEND noninteractive
ENV TERM xterm

RUN apt-get update && apt-get dist-upgrade -y && apt-get install -y \
    apt-utils curl gnupg git \
    php php-zip php-xml php-gd php-mbstring php-mysql php-curl \
    libapache2-mod-php apache2

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN curl -sL https://deb.nodesource.com/setup_8.x | bash -
RUN apt-get update && apt-get install -y nodejs

WORKDIR /var/www/budget/

COPY * ./

RUN composer install
RUN npm install

RUN chown -Rv www-data:www-data .

CMD ["apache2ctl", "-DFOREGROUND"]