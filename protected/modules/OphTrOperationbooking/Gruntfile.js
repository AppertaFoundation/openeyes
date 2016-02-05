module.exports = function(grunt) {

	/* Set the config */
	grunt.initConfig(require('./grunt/config')(grunt));

	/* Load the npm grunt tasks */
	require('matchdep').filterDev('grunt-*').forEach(grunt.loadNpmTasks);

	/* Load our custom grunt tasks */
	grunt.loadTasks('./grunt/tasks');
};