 #!/bin/bash

apt-get update
apt-get upgrade

# Install Apache2
apt-get install -y apache2
# Enable Apache2
sudo systemctl start apache2
sudo systemctl enable apache2
# Reflet Apache config
mv apache2.conf /etc/apache2/apache2.conf
mv 000-default.conf /etc/apache2/sites-available/000-default.conf

# Install PHP
apt-get install -y php7.2 php7.2-mbstring php-gd libapache2-mod-php7.2 php7.2-mysql php-common php7.2-cli php7.2-common php7.2-json php7.2-opcache php7.2-readline sendmail

# Enable PHP
a2enmod php7.2
systemctl restart apache2
