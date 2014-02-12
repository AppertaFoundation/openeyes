class dev::grunt {
	exec { 'grunt-install':
		command => '/home/vagrant/.nvm/v0.10.25/bin/npm --registry http://registry.npmjs.eu/ install -g grunt-cli',
		user => 'vagrant',
		environment => 'HOME=/home/vagrant',
		require => Exec['node-install']
	}
}
