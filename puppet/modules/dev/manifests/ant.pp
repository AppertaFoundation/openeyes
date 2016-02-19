class dev::ant {
  package { 'openjdk-7-jre':
    ensure  => present,
    require => Exec['apt-update'],
  }

  package { 'openjdk-7-jdk':
    ensure  => present,
    require => Exec['apt-update'],
  }

  package { 'ant':
    ensure  => present,
    require => Package['openjdk-7-jre', 'openjdk-7-jdk'],
  }
}