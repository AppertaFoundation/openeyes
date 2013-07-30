module.exports = {
  files: [
    '*.js',
    '*.json',
    'grunt/**/*.js'
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
    es3: false,
    es5: false,
    globals: {
      jQuery: true,
      module: false,
      require: false,
      console: false
    }
  }
};