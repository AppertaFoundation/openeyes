module.exports = function(grunt) {
  return {
    pkg: grunt.file.readJSON('package.json'),      
    compass: {
      oldstyle: {
        options: {
          sassDir: 'sass/old',
          cssDir: 'css',
          imagesPath: 'img',
          outputStyle: 'compact',
          noLineComments: true
        }
      },
      newstyle: {
        options: {
          sassDir: 'sass/new',
          cssDir: 'css',
          imagesPath: 'img',
          outputStyle: 'expanded',
          noLineComments: false
        }
      }
    },
    watch: {
      sass: {
        files: 'sass/**/*.scss',
        tasks: ['compass:newstyle']
      }
    },
    jshint: {
      files: [
        '*.js',
      ],
      options: {
        curly: true,
        eqeqeq: true,
        immed: true,
        latedef: true,
        newcap: true,
        noarg: true,
        sub: true,
        undef: true,
        unused: true,
        boss: true,
        eqnull: true,
        browser: true,
        globals: {
          jQuery: true,
          module: false,
          require: false
        }
      }
    },
  };
};