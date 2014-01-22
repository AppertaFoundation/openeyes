module.exports = function(grunt) {
	grunt.registerTask('test', 'Run the JavaScript tests', [
		'mocha'
	]);
};