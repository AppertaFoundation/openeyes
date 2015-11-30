# == Class: display::xvfb
#
# Installs and configures xvfb.
# Inspired by https://github.com/cwarden/puppet-module-xvfb.
#
# === Authors
#
# Alex Rodionov <p0deje@gmail.com>
#
# === Copyright
#
# Copyright 2013 Alex Rodionov.
#
class display::xvfb inherits display::params {
  package { $display::params::xvfb_package_name:
    alias  => 'xvfb',
    ensure => present,
  }

  file { '/etc/init.d/xvfb':
    content => template($display::params::xvfb_erb),
    mode    => '0755',
    require => Package['xvfb'],
    notify  => Service['xvfb'],
  }

  service { 'xvfb':
    ensure     => running,
    enable     => true,
    hasstatus  => true,
    hasrestart => true,
  }
}
