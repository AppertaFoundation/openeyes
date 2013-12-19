class compass {
	package { 'compass':
    provider => 'gem',
		ensure => installed,
		require => Package['rubygems'],
	}
}
