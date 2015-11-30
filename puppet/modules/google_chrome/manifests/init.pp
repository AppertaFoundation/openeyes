# == Class: google_chrome
#
# Installs the Google Chrome web browser.
#
# === Parameters
#
# [*version*]
#   Chrome version to install. Can be one of 'stable' (the default), 'unstable' or 'beta'.
#
# === Examples
#
#  include 'google_chrome'
#
#  class { google_chrome:
#    version => 'unstable',
#  }
#
#  class { google_chrome:
#    version => 'beta',
#  }
#
# === Copyright
#
# Copyright 2014 James Netherton
#
class google_chrome(
  $version  = $google_chrome::params::version
) inherits google_chrome::params {
  
  validate_re($version, ['^stable','^unstable','^beta'])

  class { 'google_chrome::config': } ->
  class { 'google_chrome::install' : }
}
