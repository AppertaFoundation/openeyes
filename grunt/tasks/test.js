var util = require('util');
var path = require('path');

module.exports = function(grunt) {
	grunt.registerMultiTask('test', 'Runs the JavaScript unit tests', function() {
		var mode = this.options().mode;
		switch(mode) {
			case 'browser':

				var options = grunt.config.get('connect.test.options');
				var url = util.format('http://%s:%s/%s', options.hostname, options.port, 'tests/js/runners');

				grunt.log.writeln('').oklns([ 'Run tests here: ' + url ].join(' '));
				grunt.task.run(['connect:test']);
				break;
			case 'headless':
				grunt.task.run(['mocha:all']);
				break;
		}
	});
};