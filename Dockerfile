FROM ubuntu:18.04

ENV DEBIAN_FRONTEND noninteractive
ENV TERM xterm

RUN apt-get update && apt-get dist-upgrade -y && apt-get install -y \
    apt-utils curl \
    php php-gd php-mbstring php-mysql php-curl libapache2-mod-php apache2
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

CMD ["apache2ctl", "-DFOREGROUND"]