Vagrant.require_version ">= 2.0.0"
Vagrant.configure("2") do |config|
  config.vm.define "ubuntu-18-04"
  config.vm.box = "bento/ubuntu-18.04"
  config.vm.provider "virtualbox" do |vb|
    vb.memory = "2048" # 2GB RAM
    vb.cpus = 1
  end

  config.vm.provision "shell", inline: "timedatectl set-timezone Asia/Tokyo"

  config.vm.synced_folder "./migration", "/home/vagrant/migration"
  config.vm.synced_folder ".", "/var/www/html", type: "virtualbox"
  config.vm.provision "file", source: "./provision/apache2/apache2.conf", destination: "apache2.conf"
  config.vm.provision "file", source: "./provision/apache2/sites-available/000-default.conf", destination: "000-default.conf"
  config.vm.provision "file", source: "./provision/mysql/provision.sql", destination: "provision.sql"

  config.vm.provision "shell", path: "./provision/scripts/web_provision.sh"
  config.vm.provision "shell", path: "./provision/scripts/db_provision.sh"

  config.vm.network "private_network", ip: "192.168.33.10"
end
