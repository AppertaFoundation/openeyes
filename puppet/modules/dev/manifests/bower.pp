class dev::bower {
	exec { 'bower-install':
		command => '/home/vagrant/.nvm/v0.10.25/bin/npm install -g bower@1.2.7',
		user => 'vagrant',
		environment => 'HOME=/home/vagrant',
		require => Exec['node-install']
	}
}
