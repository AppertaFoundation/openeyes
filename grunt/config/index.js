module.exports = function(grunt) {
	return {
		pkg: grunt.file.readJSON('package.json'),
		jshint: require('./jshint'),
		mocha: require('./mocha'),
		connect: require('./connect'),
		test: require('./test'),
		uglify: require('./uglify'),
	};
};
