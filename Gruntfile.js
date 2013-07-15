/*global module:false*/
module.exports = function(grunt) {

  /* Set the config */
  grunt.initConfig(require('./grunt/config')(grunt));

  /* Load the tasks */
  grunt.loadNpmTasks('grunt-contrib-compass');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-jshint');

  /* Register custom tasks */

  /** Checks code for syntax errors. */
  grunt.registerTask('lint', 
    ['jshint']
  );

  /* The development build task. */
  grunt.registerTask('build', 
    ['lint','compass:newstyle']
  );

  /* The default task for running `grunt`. */
  grunt.registerTask('default', 
    ['build']
  );
};