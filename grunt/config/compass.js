module.exports = {
	dist: {
		options: {
			sassDir: 'protected/assets/sass',
			cssDir: 'protected/assets/css',
			imagesDir: 'protected/assets/img',
			generatedImagesDir: 'protected/assets/img/sprites',
			outputStyle: 'expanded',
			relativeAssets: true,
			httpPath: '',
			noLineComments: false,
			importPath: [
				'protected/assets/components/foundation/scss'
			]
		}
	}
};