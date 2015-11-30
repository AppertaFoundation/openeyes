# -*- mode: ruby -*-
# vi: set ft=ruby :

$puppet_install_script = <<SCRIPT
apt-get install --yes lsb-release
DISTRIB_CODENAME=$(lsb_release --codename --short)
DEB="puppetlabs-release-${DISTRIB_CODENAME}.deb"
DEB_PROVIDES="/etc/apt/sources.list.d/puppetlabs.list"
if [ ! -e $DEB_PROVIDES ]
then
    # Print statement useful for debugging, but automated runs of this will interpret any output as an error
    # print "Could not find $DEB_PROVIDES - fetching and installing $DEB"
    wget -q http://apt.puppetlabs.com/$DEB
    sudo dpkg -i $DEB
fi
sudo apt-get update
sudo apt-get install --yes puppet=3.8.4-1puppetlabs1
SCRIPT

# quick solution (hopefully) for installing the chromedriver for use with selenium
$chromedriver_install_script = <<SCRIPT
if [ ! -e "/usr/bin/chromedriver" ]
then
    sudo apt-get install unzip
    wget -N http://chromedriver.storage.googleapis.com/2.20/chromedriver_linux64.zip -P /tmp/
    unzip -o /tmp/chromedriver_linux64.zip -d /tmp
    chmod a+x /tmp/chromedriver
    sudo mv /tmp/chromedriver /usr/local/share/chromedriver
    sudo ln -fs /usr/local/share/chromedriver /usr/bin/chromedriver
fi
SCRIPT

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

	config.vm.synced_folder "./", "/var/www", id: "vagrant-root", type: 'nfs'

    # for display
    config.vm.network :forwarded_port, guest: 5900, host: 5900

	config.vm.provider "virtualbox" do |v|
		v.customize ["modifyvm", :id, "--memory", 2024]
		# to enable selenium testing
		v.gui = true
	end

    config.vm.provision "shell", inline: $puppet_install_script
    config.vm.provision "shell", inline: $chromedriver_install_script

	config.vm.provision :puppet do |puppet|
		puppet.manifests_path = "puppet"
		puppet.manifest_file  = "default.pp"
		puppet.module_path    = "puppet/modules"
		puppet.facter         = { 'mode' => "dev", 'runsubfolder' => false }
		# puppet.options = "--verbose --debug"
	end
end
