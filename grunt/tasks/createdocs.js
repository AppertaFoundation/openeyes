module.exports = function(grunt) {
	grunt.registerMultiTask('createdocs', 'Generates the documentation', function() {
		grunt.task.run(this.options().tasks);
	});
};