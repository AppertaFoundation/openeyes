import 'classes/*'

class amp {
	exec { 'apt-update':
		command => '/usr/bin/apt-get update',
	}

	include apache2
	include mysql
	include curl
	include vim
	include mail
	include php5
	include openeyes
	include xdebug
	include composer
	include nodejs
	include grunt
	include bower
	include ruby
	include compass
}

include amp
