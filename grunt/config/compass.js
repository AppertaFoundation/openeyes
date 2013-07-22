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
      cssPath: './css',
      cssDir: 'css',
      imagesPath: './img',
      imagesDir: 'img',
      outputStyle: 'expanded',
      relativeAssets: true,
      httpPath: '',
      noLineComments: false
    }
  }
};