module.exports = function(grunt) {
	grunt.registerTask('build', 'The development build task', [
		'lint',
		'compile'
	]);
};