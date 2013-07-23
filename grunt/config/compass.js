module.exports = {
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
      imagesDir: 'img',
      generatedImagesDir: 'img/sprites',
      outputStyle: 'expanded',
      relativeAssets: true,
      httpPath: '',
      noLineComments: false
    }
  }
};