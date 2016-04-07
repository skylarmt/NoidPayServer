#!/bin/bash
if [ ! -f /mysql-configured ]; then
    /usr/bin/mysqld_safe &
    sleep 10s
    MYSQL_PASSWORD=`pwgen -c -n -1 12`
    echo mysql root password: $MYSQL_PASSWORD
    echo $MYSQL_PASSWORD > /mysql-root-pw.txt
    mysqladmin -u root password $MYSQL_PASSWORD
    sed -i -e "s/PASS_WORD/\1'${MYSQL_PASSWORD}'\2/g" /var/www/html/database_config.php
    mysql -e "source /create.sql;" -u root -p${MYSQL_PASSWORD}
    touch /mysql-configured
    killall mysqld
    sleep 10s
fi