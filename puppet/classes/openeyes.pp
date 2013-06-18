class openeyes {
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

  exec { 'create application config':
    unless  => '/usr/bin/test -e /var/www/protected/config/local/common.php',
    command => '/bin/cp /var/www/protected/config/local/common.vagrant.php /var/www/protected/config/local/common.php',
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
      create user \'openeyes\'@\'%\' identified by \'oe_test\';\
      grant all privileges on openeyes.* to \'openeyes\'@\'localhost\' identified by \'oe_test\';\
      grant all privileges on openeyes.* to \'openeyes\'@\'%\' identified by \'oe_test\';\
      flush privileges;"',
    require => Exec['create openeyes db']
  }

  exec { 'migrate openeyes db':
    command => '/usr/bin/php /var/www/protected/yiic.php migrate --interactive=0',
    require => [
      Exec['create openeyes db'],
      Exec['create openeyes user'],
      Exec['create application config'],
      File['/var/www/protected/runtime'],
    ]
  }
}
