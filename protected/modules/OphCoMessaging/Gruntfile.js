module.exports = function(grunt) {

	/* Set the config */
	grunt.initConfig(require('./grunt/config')(grunt));

	/* Load the npm grunt tasks */
	require('load-grunt-tasks')(grunt, 'grunt-*');

	/* Load our custom grunt tasks */
	grunt.loadTasks('./grunt/tasks');
};