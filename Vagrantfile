# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |config|

	vagrant_version = Vagrant::VERSION.sub(/^v/, '')
	if vagrant_version < "1.3.0"
		abort(sprintf("You need to have at least v1.3.0 of vagrant installed. You are currently using v%s", vagrant_version));
	end

	config.vm.box = "precise64"
	config.vm.box_url = "http://files.vagrantup.com/precise64.box"

    config.vm.network :forwarded_port, host: 8888, guest: 80
    config.vm.network :forwarded_port, host: 3333, guest: 3306
	config.vm.network "private_network", ip: "192.168.0.100"

	config.vm.synced_folder "./", "/var/www", id: "vagrant-root"

	config.vm.provider "virtualbox" do |v|
		v.customize ["modifyvm", :id, "--memory", 2024]
	end

	config.vm.provision :puppet do |puppet|
		puppet.manifests_path = "puppet"
		puppet.manifest_file  = "default.pp"
		puppet.module_path    = "puppet/modules"
		puppet.facter         = { 'mode' => "dev", 'runsubfolder' => false }
		# puppet.options = "--verbose --debug"
	end
end
