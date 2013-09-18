# -*- mode: ruby -*-
# vi: set ft=ruby :
# Required Vagrant version: >= 1.3.0

Vagrant.configure("2") do |config|
  config.vm.box = "precise64"
  config.vm.box_url = "http://files.vagrantup.com/precise64.box"

  config.vm.network :forwarded_port, host: 8888, guest: 80
  config.vm.network :forwarded_port, host: 3333, guest: 3306
  config.vm.network "private_network", ip: "192.168.50.4"
  config.vm.synced_folder "./", "/var/www", id: "vagrant-root", :mount_options => ["dmode=777,fmode=777"]

  config.vm.provision :puppet do |puppet|
    puppet.manifests_path = "puppet"
    puppet.options = "--verbose --debug"
  end
end
