module.exports = function(grunt) {
	return {
		dist: {
			options: {
				sassDir: 'assets/sass',
				cssDir: 'assets/css',
				imagesDir: 'assets/img',
				generatedImagesDir: 'assets/img/sprites',
				importPath: '../../../sass',
				outputStyle: 'expanded',
				relativeAssets: true,
				httpPath: '',
				noLineComments: false
			}
		}
	};
};