class vim {
	package { 'vim':
		ensure => 'present',
		require => Exec['apt-update']
	}
}
