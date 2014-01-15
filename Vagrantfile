# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |config|

	vagrant_version = Vagrant::VERSION.sub(/^v/, '')
	if vagrant_version < "1.3.0"
		abort(sprintf("You need to have at least v1.3.0 of vagrant installed. You are currently using v%s", vagrant_version));
	end

	config.vm.box = "precise64"
	config.vm.box_url = "http://files.vagrantup.com/precise64.box"

	http_port = ENV["OE_VAGRANT_HTTP_PORT"] || 8888
	http_port = http_port.to_i

	sql_port = ENV["OE_VAGRANT_SQL_PORT"] || 3333
	sql_port = sql_port.to_i

	custom_ip = ENV["OE_VAGRANT_IP"] || false

	mode = ENV["OE_VAGRANT_MODE"] || 'dev'

	if mode != 'ci' && http_port > 0
		config.vm.network :forwarded_port, host: http_port, guest: 80
	end
	if mode != 'ci' && sql_port > 0
		config.vm.network :forwarded_port, host: sql_port, guest: 3306
	end
	if custom_ip
		config.vm.network "private_network", ip: custom_ip
	end

	if mode == 'bdd' || mode == 'dev'
		config.vm.synced_folder "/tmp/behat", "/tmp/behat", id: "vagrant-root", :mount_options => ["dmode=777,fmode=777"]
	end

	config.vm.synced_folder "./", "/var/www", id: "vagrant-root", :mount_options => ["dmode=777,fmode=777"]

	config.vm.provider "virtualbox" do |v|
		v.customize ["modifyvm", :id, "--memory", 1024]
	end

	puts "Running OpenEyes install with mode: " + mode

	config.vm.provision :puppet do |puppet|
		puppet.manifests_path = "puppet"
		puppet.manifest_file  = "default.pp"
		puppet.module_path    = "puppet/modules"
		puppet.facter         = { 'mode' => mode }
		# puppet.options = "--verbose --debug"
	end
end
