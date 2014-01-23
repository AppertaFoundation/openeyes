class dev::bower {

	exec { 'bower-install':
		command => '/home/vagrant/.nvm/v0.10.23/bin/npm install -g bower',
		user => 'vagrant',
		environment => 'HOME=/home/vagrant',
		require => Exec['node-install']
	}

	# exec { 'bower-install-app-components':
	# 	command => '/bin/bash -c "source /home/vagrant/.nvm/nvm.sh && /home/vagrant/.nvm/v0.10.23/bin/bower install"',
	# 	user => 'vagrant',
	# 	cwd => '/var/www',
	# 	require => Exec['bower-install']
	# }
}
