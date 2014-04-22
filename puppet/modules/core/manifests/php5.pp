class core::php5 {
	package { 'php5':
		ensure  => present,
		require => Exec['apt-update'],
		notify  => Service['apache2']
	}

	package { 'php5-curl':
		ensure  => present,
		require => Exec['apt-update'],
		notify  => Service['apache2']
	}

	file {'/etc/php5/cli/conf.d/buffering_settings.ini':
		ensure => present,
		owner => root, group => root, mode => 444,
		content => "output_buffering = On \nzend.enable_gc = 0 \ndate.timezone = Europe/London",
	}
}