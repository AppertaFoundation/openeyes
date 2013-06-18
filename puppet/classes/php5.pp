class php5 {
  exec { 'apt-repository-php5':
    command => '/usr/bin/add-apt-repository ppa:ondrej/php5',
    require => Package['python-software-properties'],
  }

  exec { 'apt-update-php5':
    command => '/usr/bin/apt-get update',
    require => Exec['apt-repository-php5']
  }

  package { 'python-software-properties':
    ensure  => present,
    require => Exec['apt-update'],
  }

  package { 'php5':
    ensure  => present,
    require => Exec['apt-update-php5'],
    notify  => Service['apache2']
  }
}
