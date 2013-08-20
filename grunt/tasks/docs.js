module.exports = function(grunt) {
	grunt.registerMultiTask('docs', 'Generates the documentation', function() {
		grunt.task.run(this.options().tasks);
	});
};