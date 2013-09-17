class curl {
  #if ! defined(Package['curl']) {
    notice("Running curl install")

    package { 'curl':
      ensure => 'present',
      require => Exec['apt-update'],
      notify  => Service['apache2']
    }
  #}
}