module.exports = function(grunt) {

  /* Set the config */
  grunt.initConfig(require('./grunt/config')(grunt));

  /* Load the tasks */
  grunt.loadNpmTasks('grunt-contrib-compass');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-jshint');
  grunt.loadNpmTasks('grunt-contrib-clean');
  grunt.loadNpmTasks('grunt-styleguide');

  /* Register custom tasks */

  /* Spin up a connect server to display the documentation. */
  grunt.registerMultiTask('viewdocs', require('./grunt/tasks/viewdocs')(grunt)); 

  /* Generates the documentation. */
  grunt.registerTask('docs', 
    ['clean:docs', 'styleguide:dist']
  );

  /* Checks code for syntax errors. */
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