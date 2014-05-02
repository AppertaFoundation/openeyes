class core::openeyes {
	file { '/var/www/protected/runtime':
		ensure => directory,
		mode   => '0777',
	}

	file { '/var/www/assets':
		ensure => directory,
		mode   => '0777',
	}

	file { '/var/www/index.php':
		ensure => file,
		source => '/var/www/index.example.php'
	}

	file { '/var/www/index.html':
		ensure => absent,
	}

	file { '/var/www/.htaccess':
		ensure => file,
		source => '/var/www/.htaccess.sample'
	}

	file { '/var/www/protected/config/local/test.php':
		ensure => file,
		source => '/var/www/protected/config/local.sample/test.sample.php'
	}

	exec { 'create application config':
		unless  => '/usr/bin/test -e /var/www/protected/config/local/common.php',
		command => '/bin/cp /var/www/protected/config/local.sample/common.vagrant.php /var/www/protected/config/local/common.php;',
		require => Service['mysql'],
	}
	#required to provide a testdb connection id to yiic migrate
	exec { 'create console config':
		unless  => '/usr/bin/test -e /var/www/protected/config/local/console.php',
		command => '/bin/cp /var/www/protected/config/local.sample/console.vagrant.php /var/www/protected/config/local/console.php;',
		require => Service['mysql'],
	}

	exec { 'create openeyes db':
		unless  => '/usr/bin/mysql -uroot openeyes',
		command => '/usr/bin/mysql -uroot -e "create database openeyes;"',
		require => Service['mysql'],
	}

	exec { 'create openeyes user':
		unless  => '/usr/bin/mysql -uopeneyes -poe_test openeyes',
		command => '/usr/bin/mysql -uroot -e "\
			create user \'openeyes\'@\'localhost\' identified by \'oe_test\';\
			create user \'openeyes\'@\'10.0.2.2\' identified by \'oe_test\';\
			create user \'openeyes\'@\'%\' identified by \'oe_test\';\
			grant all privileges on openeyes.* to \'openeyes\'@\'localhost\' identified by \'oe_test\';\
			grant all privileges on openeyes.* to \'openeyes\'@\'10.0.2.2\' identified by \'oe_test\';\
			grant all privileges on openeyes.* to \'openeyes\'@\'%\' identified by \'oe_test\';\
			flush privileges;"',
		require => Exec['create openeyes db']
	}

	exec { 'create openeyestest db':
			unless  => '/usr/bin/mysql -uroot openeyestest',
			command => '/usr/bin/mysql -uroot -e "create database IF NOT EXISTS openeyestest ;"',
			require => Service['mysql'],
		}

		exec { 'create openeyestest user':
			unless  => '/usr/bin/mysql -uopeneyes -poe_test openeyestest',
			command => '/usr/bin/mysql -uroot -e "\
				grant all privileges on openeyestest.* to \'openeyes\'@\'localhost\' identified by \'oe_test\';\
				grant all privileges on openeyestest.* to \'openeyes\'@\'10.0.2.2\' identified by \'oe_test\';\
				grant all privileges on openeyestest.* to \'openeyes\'@\'%\' identified by \'oe_test\';\
				flush privileges;"',
			require => [
				Exec['create openeyestest db'],
				Exec['create openeyes user']
				]
		}

	exec { 'migrate openeyes db':
		command => '/usr/bin/php /var/www/protected/yiic.php migrate --interactive=0;\
		/usr/bin/php /var/www/protected/yiic.php migrate --connectionID=testdb --interactive=0;',
		require => [
			Exec['create openeyes db'],
			Exec['create openeyes user'],
			Exec['create openeyestest db'],
			Exec['create openeyestest user'],
			Exec['create application config'],
			Exec['create console config'],
			File['/var/www/protected/runtime'],
		]
	}
}
