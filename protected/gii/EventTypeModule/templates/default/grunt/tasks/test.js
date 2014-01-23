var util = require('util');
var path = require('path');

module.exports = function(grunt) {
	grunt.registerMultiTask('test', 'Runs the JavaScript unit tests', function() {
		var mode = this.options().mode;
		switch(mode) {
			case 'browser':

				var options = grunt.config.get('connect.test.options');
				var dir = util.format('modules/%s/tests/js/runners', path.basename(path.join(__dirname, '..', '..')));
				var url = util.format('http://%s:%s/%s', options.hostname, options.port, dir);

				grunt.log.writeln('').ok('Run module tests here: ' + url);

				grunt.task.run(['connect:test']);
				break;
			case 'headless':
				grunt.task.run(['mocha:test']);
				break;
		}
	});
};