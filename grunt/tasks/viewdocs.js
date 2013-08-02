module.exports = function(grunt) {
  grunt.registerMultiTask('viewdocs', 'Generates the documentation and spins up a connect server to display the documentation', function() {
    grunt.task.run(this.options().tasks);
  });
};