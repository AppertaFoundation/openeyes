# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |config|
  config.vm.box = "precise64"
  config.vm.box_url = "http://files.vagrantup.com/precise64.box"

  http_port = 8888
  http_port = ENV["OE_CUSTOM_HTTP_PORT"].to_i if ENV["OE_CUSTOM_HTTP_PORT"]

  sql_port = 3333
  sql_port = ENV["OE_CUSTOM_SQL_PORT"].to_i if ENV["OE_CUSTOM_SQL_PORT"]

  custom_ip = ''
  custom_ip = ENV["OE_CUSTOM_IP"] if ENV["OE_CUSTOM_IP"]

  if http_port != ''
    config.vm.network :forwarded_port, host: http_port, guest: 80
  end
  if sql_port != ''
    config.vm.network :forwarded_port, host: sql_port, guest: 3306
  end
  if custom_ip != ''
    config.vm.network "private_network", ip: custom_ip
  end
  config.vm.synced_folder "./", "/var/www", id: "vagrant-root", :mount_options => ["dmode=777,fmode=777"]

  config.vm.provider "virtualbox" do |v|
    v.customize ["modifyvm", :id, "--memory", 1024]
  end

  config.vm.provision :puppet do |puppet|
    puppet.manifests_path = "puppet"
    #puppet.options = "--verbose --debug"
  end
end
