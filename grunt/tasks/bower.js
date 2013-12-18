module.exports = function(grunt) {

	grunt.registerTask('bower', 'Install bower packages', function() {

		var done = this.async();

		grunt.util.spawn({
			cmd: 'bower',
			args: [ 'install' ]
		}, function(error, result) {

			if (error) {
				grunt.fail.warn('Unable to install bower packages! ' + result.toString());
				return;
			}

			grunt.log.write(result.toString());
			done();
		});
	});
};