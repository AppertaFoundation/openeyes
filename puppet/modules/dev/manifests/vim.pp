class dev::vim {
	package { 'vim':
		ensure => 'present',
		require => Exec['apt-update']
	}
}
