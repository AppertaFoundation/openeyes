class core::wkhtmltox {
	exec { "download_wkhtmltox":
		cwd => '/tmp',
		command => "/usr/bin/curl -L http://downloads.sourceforge.net/project/wkhtmltopdf/0.12.1/wkhtmltox-0.12.1_linux-precise-amd64.deb -o wkhtmltox.deb",
		creates => "/tmp/wkhtmltox.deb",
		require => Package['curl']
	}
	package { "wkhtmltox":
		provider => dpkg,
		ensure => installed,
		source => "/tmp/wkhtmltox.deb",
		require => Exec["download_wkhtmltox"]
	}
}
