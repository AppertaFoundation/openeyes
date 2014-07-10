class dev::xdebug {
	case $operatingsystem {
		Debian,Ubuntu:  { include dev::xdebug::debian}
		default: { fail "Unsupported operatingsystem ${operatingsystem}" }
	}
}

define dev::xdebug::config (
		#Template variables
		$ini_file_path    = '',
		$default_enable   = '',
		$remote_enable    = '',
		$remote_handler   = '',
		$remote_connect_back = '',
		$remote_host      = '',
		$remote_port      = '',
		$remote_autostart = '',
		$remote_log = '',
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

	$xdebug_remote_connect_back = $remote_connect_back ? {
		''      => '0',
		default => $remote_connect_back,
	}

	$xdebug_remote_port = $remote_port ? {
		''      => '9000',
		default => $remote_port,
	}

	$xdebug_remote_autostart = $remote_autostart ? {
		''      => '0',
		default => $remote_autostart,
	}

	$xdebug_remote_log = $remote_log ? {
			''      => '',
			default => $remote_log,
	}

	$xdebug_profiler_enable = $profiler_enable ? {
		''      => '0',
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
		xdebug.remote_connect_back=<%= remote_connect_back %>
		xdebug.remote_host=<%= remote_host %>
		xdebug.remote_port=<%= remote_port %>
		xdebug.remote_autostart=<%= remote_autostart %>
		xdebug.profiler_enable=<%= profiler_enable %>
		xdebug.profiler_output_name=<%= profiler_output_name %>
		xdebug.idekey=<%= idekey %>
		xdebug.remote_log=<%= remote_log %>
		xdebug.remote_handler=dbgp"),
		ensure  => present,
		require => Package['xdebug'],
		notify  => Service['apache2'],
	}
}

class dev::xdebug::debian {
	include dev::xdebug::params

	package { "xdebug":
		name   => $xdebug::params::pkg,
		ensure => installed,
		require => Class['core::php5'],
	}
}

class dev::xdebug::params {

	$pkg = $operatingsystem ? {
		/Debian|Ubuntu/ => 'php5-xdebug',
	}

	#Note: xdebug does not macke much sense without php installed
	$php = $operatingsystem ? {
		/Debian|Ubuntu/ => 'php5-cli',
	}
}
