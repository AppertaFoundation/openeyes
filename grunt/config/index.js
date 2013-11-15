module.exports = function(grunt) {
	return {
		pkg: grunt.file.readJSON('package.json'),
		compass: require('./compass'),
		watch: require('./watch'),
		jshint: require('./jshint'),
		styleguide: require('./styleguide'),
		clean: require('./clean'),
		modernizr: require('./modernizr'),
		copy: require('./copy'),
		jsdoc: require('./jsdoc'),
		docs: require('./docs'),
		hub: require('./hub')
	};
};