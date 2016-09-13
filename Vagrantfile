# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.require_version ">= 1.8.1"

require 'getoptlong'

opts = GetoptLong.new(
  [ '--hostname', GetoptLong::OPTIONAL_ARGUMENT ],
  [ '--servername', GetoptLong::OPTIONAL_ARGUMENT ]
)

hostname = 'openeyes.vm'
servername = 'OpenEyes Dev Server'

opts.each do |opt, arg|
  case opt
    when '--hostname'
      hostname = arg
    when '--servername'
      servername = arg
  end
end

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

# Check to determine whether we're on a windows or linux/os-x host,
# http://stackoverflow.com/questions/26811089/vagrant-how-to-have-host-platform-specific-provisioning-steps
module OS
  def OS.windows?
      (/cygwin|mswin|mingw|bccwin|wince|emx/ =~ RUBY_PLATFORM) != nil
  end
end

AutoNetwork.default_pool = "172.16.0.0/24"

Vagrant.configure("2") do |config|

	config.vm.box = "ubuntu/trusty64"

  config.vm.network "private_network", :auto_network => true

  if OS.windows?
    config.vm.synced_folder "./", "/var/www/openeyes", id: "vagrant-root",
      type: "rsync",
      rsync__exclude: ".git/"
  else
  	config.vm.synced_folder "./", "/var/www/openeyes", id: "vagrant-root",
      owner: "vagrant",
      group: "www-data",
      mount_options: ["dmode=775,fmode=664"]
  end

  config.vm.hostname = hostname
  config.hostsupdater.remove_on_suspend = true

  # Prefer VMware Fusion before VirtualBox
  config.vm.provider "vmware_fusion"
  config.vm.provider "virtualbox"

	config.vm.provider(:virtualbox) do |v|
		v.customize [
      "modifyvm", :id,
      "--name", servername,
      "--memory", 1024,
      "--natdnshostresolver1", "on",
      "--cpus", 2,
    ]
		v.gui = true
	end

  # VMWare Fusion
  config.vm.provider(:vmware_fusion) do |v, override|
    override.vm.box = "puppetlabs/ubuntu-14.04-64-nocm"
    v.vmx["displayname"] = servername
    v.vmx["memsize"] = "1024"
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