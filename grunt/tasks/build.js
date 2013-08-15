module.exports = function(grunt) {
  grunt.registerTask('build', 'The development build task', [
    'bower',
    'modernizr',
    'lint',
    'compile',
    'hub'
  ]);
};