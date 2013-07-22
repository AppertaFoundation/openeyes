module.exports = {
  dist: {
    options: {
      framework: {
        name: 'kss'
      },
      template: {
        src: 'docs/templates/styleguide',
        include: ''
      }
    },
    files: {
      'docs/styleguide': 'sass/new/*.scss'
    }
  }
};