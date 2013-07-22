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
  require('./grunt/tasks/docserver')(grunt);
  require('./grunt/tasks/bower')(grunt);

  grunt.registerTask('docs', 'Generates the documentation', [
    'clean:docs',
    'compass:newstyle',
    'copy:docs',
    'styleguide:dist'
  ]);

  grunt.registerTask('viewdocs', 'Spin up a connect server to view the generated documentation', [
    'docs',
    'docserver'
  ]);

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