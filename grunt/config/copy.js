module.exports = {
	docs: {
		files: [
			{
				cwd:'protected/assets',
				expand: true,
				src: [
					'css/**/*.css',
					'img/**/*',
					'js/**/*.js',
					'components/**/*',
					'modules/**/assets/**/*'
				],
				dest: 'docs/public/assets'
			},
			{
				cwd: 'docs/src/',
				expand: true,
				src: [
					'*.php',
					'fragments/**/*',
					'static-templates/**/*',

				],
				dest: 'docs/public/'
			},
			{
				cwd: 'docs/src/assets',
				expand: true,
				src: [
					'js/**/*',
					'css/**/*',
					'img/**/*'
					],
				dest: 'docs/public/assets'
			}
		]
	}
};