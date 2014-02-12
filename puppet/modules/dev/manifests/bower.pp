class dev::bower {

	exec { 'bower-install':
		command => '/home/vagrant/.nvm/v0.10.25/bin/npm --registry http://registry.npmjs.eu/ install -g bower@1.2.7',
		user => 'vagrant',
		environment => 'HOME=/home/vagrant',
		require => Exec['node-install']
	}

	# Until we remove the bower components from the repo, this is not necessary to run.
	# exec { 'bower-install-app-components':
	# 	command => '/bin/bash -c "source /home/vagrant/.nvm/nvm.sh && /home/vagrant/.nvm/v0.10.25/bin/bower install"',
	# 	user => 'vagrant',
	# 	cwd => '/var/www',
	# 	require => Exec['bower-install']
	# }
}
