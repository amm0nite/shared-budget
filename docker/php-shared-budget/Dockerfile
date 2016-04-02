FROM ubuntu:latest

ENV DEBIAN_FRONTEND noninteractive
ENV TERM xterm

RUN apt-get update && apt-get dist-upgrade -y && apt-get install -y \
  build-essential libssl-dev git curl wget nano \
  apache2 mysql-client php5 php5-mcrypt php5-mysql php5-xdebug

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN curl -o- https://raw.githubusercontent.com/creationix/nvm/v0.31.0/install.sh | bash
RUN bash -c "source /root/.nvm/nvm.sh && nvm install 5 && npm install -g bower"

RUN a2dissite 000-default
RUN rm /etc/apache2/sites-available/000-default.conf
RUN rm /etc/apache2/sites-available/default-ssl.conf
ADD budget.conf /etc/apache2/sites-available/budget.conf
RUN a2ensite budget.conf
RUN a2enmod rewrite

RUN mkdir -p /var/budget
EXPOSE 80

ADD apache2.sh /usr/local/bin/apache2exe
RUN chmod u+x /usr/local/bin/apache2exe
CMD [ "apache2exe" ]
