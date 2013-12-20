class dev::nodejs {

	exec { 'nvm-install':
		command => '/usr/bin/curl https://raw.github.com/creationix/nvm/master/install.sh | /bin/sh',
		creates => '/home/vagrant/.nvm',
		user => 'vagrant',
		environment => 'HOME=/home/vagrant',
		require => Package['curl']
	}

	exec { 'node-install':
		command => '/bin/bash -c "source /home/vagrant/.nvm/nvm.sh && nvm install 0.10.23 && nvm alias default 0.10.23"',
		user => 'vagrant',
		environment => 'HOME=/home/vagrant',
		require => Exec['nvm-install']
	}
}
