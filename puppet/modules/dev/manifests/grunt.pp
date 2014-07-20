class dev::grunt {
	exec { 'grunt-install':
		command => '/bin/bash -c "source /home/vagrant/.nvm/nvm.sh && npm install -g grunt-cli@0.1.13"',
		user => 'vagrant',
		environment => 'HOME=/home/vagrant',
		require => Exec['node-install']
	}
}
