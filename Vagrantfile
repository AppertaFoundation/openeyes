# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.require_version ">= 1.5"

PLUGINS = %w(vagrant-auto_network vagrant-hostsupdater vagrant-cachier)

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

	config.vm.synced_folder "./", "/var/www/openeyes", id: "vagrant-root",
    owner: "vagrant",
    group: "www-data",
    mount_options: ["dmode=775,fmode=664"]

  config.vm.hostname = "openeyes.vm"
  config.hostsupdater.remove_on_suspend = true

  # Prefer VMware Fusion before VirtualBox
  config.vm.provider "vmware_fusion"
  config.vm.provider "virtualbox"

	config.vm.provider "virtualbox" do |v, override|
		v.customize [
      "modifyvm", :id,
      "--name", "OpenEyes Server",
      "--memory", 2048,
      "--natdnshostresolver1", "on",
      "--cpus", 2,
    ]
		v.gui = true

		# override.vm.synced_folder "./", "/var/www/openeyes", id: "vagrant-root",
		# 	owner: "vagrant",
		# 	group: "www-data",
		# 	mount_options: ["dmode=775,fmode=664"]
	end

  # VMWare Fusion
  config.vm.provider "vmware_fusion" do |v, override|
    override.vm.box = "puppetlabs/ubuntu-14.04-64-nocm"
    v.vmx["displayname"] = "OpenEyes Server"
    v.vmx["memsize"] = "2048"
    v.vmx["numvcpus"] = "2"
    # v.gui = true
  end

  config.vm.provision "ansible_local" do |ansible|
    ansible.playbook = "ansible/playbook.yml"
    # ansible.verbose = "vvv" # Debug
  end

  config.cache.synced_folder_opts = {
    mount_options: ["rw", "vers=3", "tcp", "nolock"]
  }

end