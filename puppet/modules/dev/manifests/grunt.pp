class dev::grunt {
	exec { 'grunt-install':
		command => '/home/vagrant/.nvm/v0.10.23/bin/npm install -g grunt-cli',
		user => 'vagrant',
		environment => 'HOME=/home/vagrant',
		require => Exec['node-install']
	}
}
