class core::mysql {
	package { 'mysql-server':
		ensure  => present,
		require => Exec['apt-update'],
	}

	package { 'php5-mysql':
		ensure  => present,
		require => Package['php5'],
		notify  => Service['apache2'],
	}

	service { 'mysql':
		ensure  => running,
		require => Package['mysql-server'],
	}

	file { 'mysql configuration':
		path    => '/etc/mysql/conf.d/conf.cnf',
		ensure  => present,
		content => "\
[mysqld]
lower_case_table_names = 1
bind-address = 0.0.0.0
skip-external-locking = FALSE
",
		require => Package['mysql-server'],
		notify  => Service['mysql'],
		mode    => 644,
	}
}
