module.exports = function(grunt) {
	return {
		test: {
			options: {
				hostname: '127.0.0.1',
				port: 8000,
				base: '../../',
				directory: '../../',
				keepalive: true
			}
		}
	}
};