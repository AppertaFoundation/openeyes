module.exports = {
  default: {
    src: ['protected/modules/*/Gruntfile.js'],
    tasks: [
      'default'
    ]
  },
  build: {
    src: ['protected/modules/*/Gruntfile.js'],
    tasks: [
      'build'
    ]
  }
};