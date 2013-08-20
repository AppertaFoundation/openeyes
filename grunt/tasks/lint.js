module.exports = function(grunt) {
	grunt.registerTask('lint', 'Check code for syntax errors', [
		'jshint'
	]);
};