module.exports = function(grunt) {
	grunt.registerTask('compile', 'Compiles the public assets', [
		'copy:compass',
        'compass:dist'
	]);
};