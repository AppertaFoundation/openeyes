var path = require('path');

module.exports = function(grunt) {
	return {
		dist: {
			options: {
				sassDir: 'assets/sass',
				cssDir: 'assets/css',
				imagesDir: 'assets/img',
				generatedImagesDir: 'assets/img/sprites',
				importPath: [
					path.join('..', '..', '..', 'protected', 'assets', 'sass'),
					path.join('..', '..', '..', 'protected', 'assets', 'components', 'foundation', 'scss')
				],
				outputStyle: 'expanded',
				relativeAssets: true,
				httpPath: '',
				noLineComments: false
			}
		}
	};
};