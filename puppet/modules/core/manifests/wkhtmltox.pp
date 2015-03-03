class core::wkhtmltox {
	package { "wkhtmltopdf":
    ensure  => present,
    require => Exec['apt-update'],
	}
}
