import 'classes/*'

    class amp {
        exec { 'apt-update':
        command => '/usr/bin/apt-get update',
    }

    include apache2
    include mysql
    include curl
    include php5
    include openeyes
    include xdebug
    include composer
}

include amp
