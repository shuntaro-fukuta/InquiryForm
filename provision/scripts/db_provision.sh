#!/bin/bash

# Install MySQL
sudo apt install -y mysql-server
mv my.cnf /etc/mysql/my.cnf
systemctl enable mysql
systemctl start mysql

mysql -u root --connect-expired-password < provision.sql

migration_files="/home/vagrant/migration/*"
for migration_file in $migration_files; do
  mysql -u root --connect-expired-password < $migration_file
done
