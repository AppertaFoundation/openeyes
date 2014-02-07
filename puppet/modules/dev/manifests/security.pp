class dev::security {

	file {'/etc/security/limits.d/nproc.conf':
		ensure => present,
		owner => root, group => root, mode => 444,
		content => "vagrant 	soft	nproc	25000
vagrant 	hard	nproc	30000
vagrant 	soft 	nofile 4096
vagrant 	hard 	nofile 10240",
	}

}