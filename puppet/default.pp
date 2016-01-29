node default {

  exec { 'apt-mirror':
    command => "/bin/sed -i 's,us.archive.ubuntu.com/ubuntu,mirror.vorboss.net/ubuntu-archive,' /etc/apt/sources.list",
  }

	exec { 'apt-update':
		command => '/usr/bin/apt-get update',
    require => Exec['apt-mirror'],
	}

  class { 'wkhtmltox':
    ensure => 'present'
  }

  class {'display':}

  class { 'selenium::server':}

  include 'google_chrome'

  include core::apache2
	include core::mysql
	include core::curl
	include core::git
	include core::mail
	include core::php5
	include core::openeyes
	include core::composer

	if $mode == 'dev' {
		include dev::vim
		include dev::xdebug
		include dev::nodejs
		include dev::grunt
		include dev::bower
		include dev::ruby
		include dev::compass
		include dev::security
		notice("Running advanced xdebug config")
		dev::xdebug::config { 'default':
			profiler_output_name => 'xdebug.log',
			remote_connect_back => 1,
			remote_enable => 1,
			remote_port => 9000
		}
    include dev::ant
	}

	if $mode == 'ci' {
		include dev::security
		include dev::xdebug
		notice("Running advanced xdebug config")
		dev::xdebug::config { 'default':
			profiler_output_name => 'xdebug.log'
		}
	}

	core::apache2::loadmodule{"rewrite": }

	core::apache2::loadmodule{"version": }

}
