class curl {
  if ! defined(Package['curl']) {
    package { 'curl':
      ensure => 'present',
      require => Exec['apt-update'],
      notify  => Service['apache2']
    }
  }
}