module.exports = function(grunt) {

		'use strict';

		/* Set the config */
		grunt.initConfig(require('./grunt/config')(grunt));

		/* Load the npm grunt tasks */
		require('load-grunt-tasks')(grunt, 'grunt-*');

        grunt.loadNpmTasks('grunt-contrib-compass');
        grunt.loadNpmTasks('grunt-contrib-uglify-es');

		/* Load our custom grunt tasks */
		grunt.loadTasks('./grunt/tasks');

        grunt.registerTask('default', ['uglify']);
};
