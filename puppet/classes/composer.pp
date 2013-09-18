class composer {
    exec { "install_composer":
        command => "/usr/bin/curl -sS https://getcomposer.org/installer | /usr/bin/php; mv composer.phar /usr/local/bin/composer",
        path    => "/usr/local/bin/:/bin/",
        require => Package['curl'],
        # path    => [ "/usr/local/bin/", "/bin/" ],  # alternative syntax
    }

    exec { "run_composer_build":
        command => "composer update; composer install",
        path    => "/usr/local/bin/:/bin/:/usr/bin/",
        cwd => '/var/www',
        require  => Exec["install_composer"]
    }
}