module.exports = function(grunt) {
	grunt.registerTask('build', 'The development build task', [
		'modernizr',
		'lint',
		'compile'
	]);
};