module.exports = function(grunt) {

  /* Set the config */
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    compass: require('./grunt/config/compass'),
    watch: require('./grunt/config/watch'),
    jshint: require('./grunt/config/jshint'),
    styleguide: require('./grunt/config/styleguide'),
    clean: require('./grunt/config/clean'),
    viewdocs: require('./grunt/config/viewdocs'),
    modernizr: require('./grunt/config/modernizr'),
    copy: require('./grunt/config/copy')
  });

  /* Load the node packaged grunt tasks */
  grunt.loadNpmTasks('grunt-contrib-compass');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-jshint');
  grunt.loadNpmTasks('grunt-contrib-clean');
  grunt.loadNpmTasks('grunt-contrib-copy');
  grunt.loadNpmTasks('grunt-styleguide');
  grunt.loadNpmTasks('grunt-modernizr');

  /* Load our custom grunt tasks */
  require('./grunt/tasks/viewdocs')(grunt);
  require('./grunt/tasks/bower')(grunt);

  /* Generates the documentation. */
  grunt.registerTask('docs', [
    'clean:docs',
    'compass:newstyle',
    'copy:docs',
    'styleguide:dist'
  ]);

  /* Checks code for syntax errors. */
  grunt.registerTask('lint', [
    'jshint'
  ]);

  /* The development build task. */
  grunt.registerTask('build', [
    'bower',
    'modernizr',
    'lint',
    'compass:newstyle'
  ]);

  /* The default task for running `grunt`. */
  grunt.registerTask('default', [
    'build'
  ]);
};