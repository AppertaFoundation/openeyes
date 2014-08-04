module.exports = function(grunt) {
	return {
		pkg: grunt.file.readJSON('package.json'),
		compass: require('./compass')(grunt),
		watch: require('./watch')(grunt),
		mocha: require('./mocha')(grunt),
		connect: require('./connect')(grunt),
		test: require('./test')(grunt)
	};
};