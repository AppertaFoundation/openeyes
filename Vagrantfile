# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.require_version ">= 1.5"

PLUGINS = %w(vagrant-auto_network vagrant-hostsupdater)

PLUGINS.reject! { |plugin| Vagrant.has_plugin? plugin }

unless PLUGINS.empty?
  print "The following plugins will be installed: #{PLUGINS.join ", "} continue? [Y/n]: "
  unless ['no', 'n'].include? $stdin.gets.strip.downcase
    PLUGINS.each do |plugin|
      system("vagrant plugin install #{plugin}")
      puts
    end
  end
  puts "Please run again"
  exit 1
end

AutoNetwork.default_pool = "172.16.0.0/24"

Vagrant.configure("2") do |config|

	config.vm.box = "ubuntu/trusty64"

  config.vm.network "private_network", :auto_network => true

	config.vm.synced_folder "./", "/var/www/openeyes", id: "vagrant-root"

  config.vm.hostname = "openeyes.vm"
  config.hostsupdater.remove_on_suspend = true

  # Prefer VMware Fusion before VirtualBox
  config.vm.provider "vmware_fusion"
  config.vm.provider "virtualbox"

	config.vm.provider "virtualbox" do |v|
		v.customize [
      "modifyvm", :id,
      "--name", "OpenEyes Server",
      "--memory", 2048,
      "--natdnshostresolver1", "on",
      "--cpus", 2,
    ]
		v.gui = true
	end

  # VMWare Fusion
  config.vm.provider "vmware_fusion" do |v, override|
    override.vm.box = "puppetlabs/ubuntu-14.04-64-nocm"
    v.vmx["displayname"] = "OpenEyes Server"
    v.vmx["memsize"] = "2048"
    v.vmx["numvcpus"] = "2"
    # v.gui = true
  end

  config.vm.provision "ansible" do |ansible|
    ansible.playbook = "ansible/playbook.yml"
  end


 #  config.puppet_install.puppet_version = :latest
 #  # config.vm.provision "puppet"
	# config.vm.provision :puppet do |puppet|
 #    puppet.environment = "development"
 #    puppet.environment_path = "manifests/environments"
	# # 	puppet.manifests_path = "puppet"
	# # 	# puppet.manifest_file  = "default.pp"
	# # 	# puppet.module_path    = "puppet/modules"
	# # 	# puppet.facter         = { 'mode' => "dev" }
	# 	puppet.options        = "--verbose --debug"
	# end
end