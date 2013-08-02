module.exports = function(grunt) {

  /* Set the config */
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    compass: require('./grunt/config/compass'),
    watch: require('./grunt/config/watch'),
    jshint: require('./grunt/config/jshint'),
    styleguide: require('./grunt/config/styleguide'),
    clean: require('./grunt/config/clean'),
    docserver: require('./grunt/config/docserver'),
    modernizr: require('./grunt/config/modernizr'),
    copy: require('./grunt/config/copy'),
    jsdoc: require('./grunt/config/jsdoc'),
    docs: require('./grunt/config/docs'),
    viewdocs: require('./grunt/config/viewdocs')
  });

  /* Load the node packaged grunt tasks */
  grunt.loadNpmTasks('grunt-contrib-compass');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-jshint');
  grunt.loadNpmTasks('grunt-contrib-clean');
  grunt.loadNpmTasks('grunt-contrib-copy');
  grunt.loadNpmTasks('grunt-styleguide');
  grunt.loadNpmTasks('grunt-jsdoc');
  grunt.loadNpmTasks('grunt-modernizr');

  /* Load our custom grunt tasks */
  require('./grunt/tasks/docserver')(grunt);
  require('./grunt/tasks/bower')(grunt);
  require('./grunt/tasks/docs')(grunt);
  require('./grunt/tasks/viewdocs')(grunt);

  grunt.registerTask('lint', 'Check code for syntax errors', [
    'jshint'
  ]);

  grunt.registerTask('compile', 'Compiles the public assets', [
    'compass:newstyle'
  ]);

  grunt.registerTask('build', 'The development build task', [
    'bower',
    'modernizr',
    'lint',
    'compile'
  ]);

  grunt.registerTask('default', 'The default task', [
    'build'
  ]);
};