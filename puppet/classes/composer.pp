class composer {
    package { 'git':
        ensure  => present,
        require => Exec['apt-update']
    }

    exec { "install_composer":
        command => "/usr/bin/curl -sS https://getcomposer.org/installer | /usr/bin/php; mv composer.phar /usr/local/bin/composer",
        path    => "/usr/local/bin/:/bin/",
        require => Package['curl','git']
    }

    exec { "run_composer_build":
        command => "composer install --prefer-source --verbose --no-interaction",
        path    => "/usr/local/bin/:/bin/:/usr/bin/",
        cwd => '/var/www',
        require  => Exec["install_composer"]
    }
}
