FROM ubuntu:trusty

MAINTAINER Skylar Ittner <admin@netsyms.com>

RUN apt-get update && apt-get upgrade -y
RUN DEBIAN_FRONTEND=noninteractive apt-get -y install mysql-client mysql-server apache2 libapache2-mod-php5 pwgen python-setuptools vim-tiny nano php5-mysql php5-gd php5-apcu curl git

# nuke the webroot
WORKDIR /var/www
RUN rm -rf html && mkdir html
WORKDIR /var/www/html

# install the server (private repo for now, thx GitHub Student)
RUN git clone https://skylarmt-script-account:scriptb0t@github.com/skylarmt/NoidPayServer.git .

# 0wn3d :)
RUN cd .. && chown -R www-data:www-data html

# Stole this from someone else's PHP/MySQL project
ADD ./docker/start_container.sh /start_container.sh
RUN chmod 755 /start_container.sh
# Gimme some SQL
ADD ./docker/create.sql /create.sql

# I CAN HAS PORTZ?
EXPOSE 80 3306

CMD ["/bin/bash", "/start_container.sh"]