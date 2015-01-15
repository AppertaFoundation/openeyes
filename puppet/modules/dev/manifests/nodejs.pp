class dev::nodejs {

	exec { 'nvm-install':
		command => '/usr/bin/curl https://raw.githubusercontent.com/creationix/nvm/master/install.sh | /bin/sh',
		creates => '/home/vagrant/.nvm',
		user => 'vagrant',
		environment => 'HOME=/home/vagrant',
		require => Package['curl','git'] # nvm install script uses git to download nvm
	}

	exec { 'node-install':
		command => '/bin/bash -c "source /home/vagrant/.nvm/nvm.sh && nvm install 0.10 && nvm alias default 0.10"',
		user => 'vagrant',
		environment => 'HOME=/home/vagrant',
		require => Exec['nvm-install']
	}

	exec { 'npm-install-app-modules':
		command => '/bin/bash -c "source /home/vagrant/.nvm/nvm.sh && npm install"',
		user => 'vagrant',
		cwd => '/var/www',
		environment => 'HOME=/home/vagrant',
		require => Exec['node-install']
	}
}
