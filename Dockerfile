FROM ubuntu:trusty

MAINTAINER Skylar Ittner <admin@netsyms.com>

# Install packages
RUN echo "Installing packages..." && apt-get update && apt-get -y install mysql-client mysql-server apache2 libapache2-mod-php5 pwgen python-setuptools vim-tiny phpmyadmin php5-mysql php5-curl php5-gd php5-apcu nodejs npm curl git
ADD ./docker/foreground.sh /etc/apache2/foreground.sh
RUN chmod 755 /etc/apache2/foreground.sh
# Install Composer
RUN echo "Installing Composer..." && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer


# nuke the webroot
WORKDIR /var/www
RUN echo "Setting up web root..." && rm -rf html && mkdir html
WORKDIR /var/www/html

# install the server (private repo for now, thx GitHub Student)
RUN echo "Cloning latest from Git..." && git clone https://skylarmt-script-account:scriptb0t@github.com/skylarmt/NoidPayServer.git .
RUN rm -rf vendor && composer install
# 0wn3d :)
RUN cd .. && chown -R www-data:www-data html

# Stole this from someone else's PHP/MySQL project
ADD ./docker/start_container.sh /start_container.sh
RUN echo "Finishing setup and starting container..." && chmod 755 /start_container.sh
# Gimme some SQL
ADD ./docker/create.sql /create.sql

# I CAN HAS PORTZ?
EXPOSE 80
EXPOSE 443
EXPOSE 3306

CMD ["/bin/bash", "/start_container.sh"]
