module.exports = function(grunt) {
	return {
		dist: {
			options: {
				sassDir: 'assets/sass',
				cssDir: 'assets/css',
				imagesDir: 'assets/img/new',
				generatedImagesDir: 'assets/img/sprites',
				importPath: '../../../sass/new',
				outputStyle: 'expanded',
				relativeAssets: true,
				httpPath: '',
				noLineComments: false
			}
		}
	};
};