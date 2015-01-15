class core::git {
	package { 'git':
		ensure => present,
		require => Exec['apt-update']
	}
}