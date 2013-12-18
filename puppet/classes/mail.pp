class mail {
    package { 'postfix':
        ensure  => present,
        require => Exec['apt-update'],
    }

    package { 'mailutils':
        ensure  => present,
        require => Exec['apt-update'],
    }
}
