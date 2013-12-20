class core::curl {
	package { 'curl':
		ensure => 'present',
		require => Exec['apt-update'],
		notify  => Service['apache2']
	}
}
