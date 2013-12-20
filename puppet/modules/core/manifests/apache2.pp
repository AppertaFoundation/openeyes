class core::apache2 {
	package { 'apache2':
		ensure  => present,
		require => Exec['apt-update'],
	}

	service { 'apache2':
		ensure  => running,
		require => Package['apache2'],
	}

	file { '/etc/apache2/mods-enabled/rewrite.load':
		 ensure => 'link',
		 target => '/etc/apache2/mods-available/rewrite.load',
		 require => Package['apache2'],
		 notify  => Service['apache2']
	}

	file { 'default virtualhost':
		path    => '/etc/apache2/sites-available/default',
		ensure  => present,
		content => "\
<VirtualHost *:80>
	DocumentRoot /var/www
	ServerName localhost

	<Directory />
		Options FollowSymLinks
		AllowOverride All
	</Directory>

	ErrorLog /var/log/apache2/error.log
	LogLevel warn
	CustomLog /var/log/apache2/access.log combined
	ServerSignature Off
	EnableSendfile Off
</VirtualHost>",
		require => Package['apache2'],
		notify  => Service['apache2'],
		mode    => 644,
	}
}
