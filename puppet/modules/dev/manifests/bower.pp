class dev::bower {
	exec { 'bower-install':
		command => '/home/vagrant/.nvm/v0.10.23/bin/npm install -g bower',
		user => 'vagrant',
		environment => 'HOME=/home/vagrant',
		require => Exec['node-install']
	}
}
