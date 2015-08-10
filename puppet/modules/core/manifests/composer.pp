class core::composer {

	if $runsubfolder == 'yes' {
		exec { "download_composer":
			cwd => '/var/www/subfolder',
			command => "/usr/bin/curl -sS https://getcomposer.org/installer | /usr/bin/php",
			creates => "/var/www/subfolder/composer.phar",
			require => Package['curl','git', 'php5-cli']
		}

		exec { "run_composer_install":
			cwd => '/var/www',
			command => "/var/www/subfolder/composer.phar install --prefer-source --no-interaction --working-dir /var/www/subfolder",
			require => Exec["download_composer"],
			timeout => 600, # This can take a long time
		}
	}
	else{
		exec { "download_composer":
			cwd => '/var/www',
			command => "/usr/bin/curl -sS https://getcomposer.org/installer | /usr/bin/php &&  cp composer.phar /usr/local/bin/composer",
			creates => "/var/www/composer.phar",
			require => Package['curl','git', 'php5-cli']
		}

		exec { "run_composer_install":
			cwd => '/var/www',
			command => "/var/www/composer.phar update --prefer-source --no-interaction --working-dir /var/www",
			#command => "/var/www/composer.phar install --no-interaction",
      require => [Exec["download_composer"], Package['php5-curl', 'php5-cli']],
			timeout => 0, # This can take a long time
		}
	}
}
