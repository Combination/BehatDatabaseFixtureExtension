#!/usr/bin/env bash
sudo debconf-set-selections <<< "mysql-server mysql-server/root_password password empty"
sudo debconf-set-selections <<< "mysql-server mysql-server/root_password_again password empty"
sudo aptitude install -q -y -f  mysql-server mysql-client php5-mysql
mysql -uroot -pempty -e 'CREATE DATABASE `fixtures` CHARACTER SET `utf8` COLLATE `utf8_bin`;'
mysql -uroot -pempty -e "CREATE USER 'super'@'%' IDENTIFIED BY 'empty'; grant all on *.* to 'super'@'%' with grant option;"