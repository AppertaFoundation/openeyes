class dev::grunt {
	exec { 'grunt-install':
		command => '/home/vagrant/.nvm/v0.10.25/bin/npm --registry http://registry.npmjs.eu/ install -g grunt-cli@0.1.13',
		user => 'vagrant',
		environment => 'HOME=/home/vagrant',
		require => Exec['node-install']
	}
}
