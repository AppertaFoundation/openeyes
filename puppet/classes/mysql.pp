class mysql {
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

  file { 'mysql lowercase_tables':
    path    => '/etc/mysql/conf.d/lowercase_tables.cnf',
    ensure  => present,
    content => "\
[mysqld]
lower_case_table_names = 1",
    require => Package['mysql-server'],
    notify  => Service['mysql'],
    mode    => 644,
  }
}
