class dev::ruby {
	package { 'ruby':
		ensure  => present,
		require => Exec['apt-update'],
	}

	package { 'rubygems':
		ensure  => present,
		require => Package['ruby'],
	}
}
