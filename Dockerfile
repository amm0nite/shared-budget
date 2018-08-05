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

COPY /  /var/www/budget/
WORKDIR /var/www/budget/

RUN composer install
RUN npm install --unsafe-perm

COPY budget.conf /etc/apache2/sites-available
RUN a2ensite budget.conf
RUN a2dissite 000-default.conf
RUN a2enmod rewrite

RUN chown -Rv www-data:www-data var/cache
RUN chown -Rv www-data:www-data var/logs

CMD ["apache2ctl", "-DFOREGROUND"]