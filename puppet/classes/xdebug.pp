class xdebug {
    case $operatingsystem {
        Debian,Ubuntu:  { include xdebug::debian}
        default: { fail "Unsupported operatingsystem ${operatingsystem}" }
    }

    define config (
        #Template variables
        $ini_file_path    = '',
        $default_enable   = '',
        $remote_enable    = '',
        $remote_handler   = '',
        $remote_host      = '',
        $remote_port      = '',
        $remote_autostart = '',
        $profiler_enable = '',
        $profiler_output_name = '',
        $idekey = '',
      )
    {
        notice("Define xdebug config \$ini_file_path ${ini_file_path}, \$remote_enable ${remote_enable}, \$remote_host ${remote_host}, ")

        #Template variables default values
        $xdebug_ini_file_path = $ini_file_path ? {
            ''      => '/etc/php5/conf.d/xdebug_config.ini',
            default => $ini_file_path,
        }

        $xdebug_default_enable = $default_enable ? {
            ''      => '1',
            default => $default_enable,
        }

        $xdebug_remote_enable = $remote_enable ? {
            ''      => '1',
            default => $remote_enable,
        }

        $xdebug_remote_handler = $remote_handler ? {
            ''      => 'dbgp',
            default => $remote_handler,
        }

        $xdebug_remote_host = $remote_host ? {
            ''      => 'localhost',
            default => $remote_host,
        }

        $xdebug_remote_port = $remote_port ? {
            ''      => '9000',
            default => $remote_port,
        }

        $xdebug_remote_autostart = $remote_autostart ? {
            ''      => '1',
            default => $remote_autostart,
        }

        $xdebug_profiler_enable = $profiler_enable ? {
            ''      => '1',
            default => $profiler_enable,
        }

        $xdebug_profiler_output_name = $profiler_output_name ? {
            ''      => '',
            default => $profiler_output_name,
        }

        $xdebug_idekey = $idekey ? {
            ''      => 'XDEBUG_ECLIPSE',
            default => $idekey,
        }

        file { "$xdebug_ini_file_path" :
            content => inline_template("\
            xdebug.default_enable=<%= default_enable %>
            xdebug.remote_enable=<%= remote_enable %>
            xdebug.remote_handler=<%= remote_handler %>
            xdebug.remote_host=<%= remote_host %>
            xdebug.remote_port=<%= remote_port %>
            xdebug.remote_autostart=<%= remote_autostart %>
            xdebug.profiler_enable=<%= profiler_enable %>
            xdebug.profiler_output_name=<%= profiler_output_name %>
            xdebug.idekey=<%= idekey %>
            xdebug.remote_handler=dbgp"),
            ensure  => present,
            require => Package['xdebug'],
            notify  => Service['apache2'],
        }
    }

}

class xdebug::debian {

    include xdebug::params

    package { "xdebug":
        name   => $xdebug::params::pkg,
        ensure => installed,
        require => Class['php5'],
    }

}

class xdebug::params {

    $pkg = $operatingsystem ? {
        /Debian|Ubuntu/ => 'php5-xdebug',
    }

    #Note: xdebug does not macke much sense without php installed
    $php = $operatingsystem ? {
        /Debian|Ubuntu/ => 'php5-cli',
    }
}
notice("Running advanced xdebug config")
xdebug::config { 'default':
        remote_host => '192.168.50.1', # Vagrant users can specify their address
        remote_port => '9000', # Change default settings
        remote_autostart => '1',
        remote_enable => '1',
        profiler_enable => '1',
        profiler_output_name => 'xdebug.log',
        idekey => 'PHPSTORM',
        #remote_handler => 'dbgp',
}

