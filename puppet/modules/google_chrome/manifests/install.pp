class google_chrome::install() inherits google_chrome::params {
  package { "${google_chrome::params::package_name}-${google_chrome::version}":,
    ensure => $google_chrome::params::ensure,
  }
}
