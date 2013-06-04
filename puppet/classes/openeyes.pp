class openeyes {
  file { '/var/www/protected/runtime':
    ensure => directory,
    mode   => '0777',
  }

  file { '/var/www/assets':
    ensure => directory,
    mode   => '0777',
  }

  file { '/var/www/protected/config/local/common.php':
    ensure => file,
    source => '/var/www/protected/config/local/common.sample.php',
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

  exec { 'create-openeyes-db':
    unless  => '/usr/bin/mysql -uroot openeyes',
    command => '/usr/bin/mysql -uroot -e "create database openeyes;"',
    require => Service['mysql'],
  }

  exec { 'migrate-openeyes-db':
    command => '/usr/bin/php /var/www/protected/yiic.php migrate --interactive=0',
    require => [
      Exec['create-openeyes-db'],
      File['/var/www/protected/config/local/common.php'],
      File['/var/www/protected/runtime'],
    ]
  }
}
