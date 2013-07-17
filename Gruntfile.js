module.exports = function(grunt) {

  /* Set the config */
  grunt.initConfig(require('./grunt/config')(grunt));

  /* Load the grunt packaged tasks */
  grunt.loadNpmTasks('grunt-contrib-compass');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-jshint');
  grunt.loadNpmTasks('grunt-contrib-clean');
  grunt.loadNpmTasks('grunt-styleguide');
  grunt.loadNpmTasks('grunt-modernizr');

  /* Load in custom non-packaged grunt tasks */
  require('./grunt/tasks')(grunt);  
};