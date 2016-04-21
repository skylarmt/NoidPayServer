#!/bin/bash
if [ ! -f /mysql-configured ]; then
    /usr/bin/mysqld_safe &
    sleep 10s
    MYSQL_PASSWORD=`pwgen -c -n -1 12`
    echo mysql root password: $MYSQL_PASSWORD
    echo $MYSQL_PASSWORD > /mysql-root-pw.txt
    mysqladmin -u root password $MYSQL_PASSWORD
    sed -i -e "s/PASS_WORD/${MYSQL_PASSWORD}/" /var/www/html/database_config.php.new
    mv /var/www/html/database_config.php.new /var/www/html/database_config.php
    mysql -e "source /create.sql;" -u root -p${MYSQL_PASSWORD}
    touch /mysql-configured
    killall mysqld
    sleep 10s
fi
/etc/init.d/mysql restart
/etc/init.d/apache2 restart
