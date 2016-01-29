class google_chrome::params() {
  $ensure        = installed
  $version       = 'stable'
  $package_name  = 'google-chrome'
  $repo_gpg_key  = 'http://dl-ssl.google.com/linux/linux_signing_key.pub'
  $repo_name     = 'google-chrome'

  case $::osfamily {
    'RedHat', 'Suse': {
      $repo_base_url = 'http://dl.google.com/linux/chrome/rpm/stable/$basearch'
    }
    'Debian': {
      $repo_base_url = 'http://dl.google.com/linux/chrome/deb/'
    }
    default: {
      fail("Unsupported operating system family ${::osfamily}")
    }
  }
}
