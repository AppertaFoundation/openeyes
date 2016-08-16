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
				'protected/assets/components/font-awesome/scss',
				'protected/assets/components/material-design-lite/src',
				'protected/assets/components/foundation/scss'
			]
		}
	}
};
