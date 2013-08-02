module.exports = {
  styleguide: {
    options: {
      tasks: [
        'clean:styleguide',
        'compass:newstyle',
        'copy:docs',
        'styleguide:dist'
      ]
    }
  },
  javascript: {
    options: {
      tasks: [
        'clean:javascript',
        'jsdoc:dist'
      ]
    }
  }
};