module.exports = {
	docs: {
		files: [
			{
				cwd: 'protected/assets',
				expand: true,
				src: [
					'css/**/*.css',
					'img/**/*',
					'js/**/*.js',
					'components/**/*',
				],
				dest: 'docs/public/assets'
			},
			{
				cwd: 'protected',
				expand: true,
				src: [
					'modules/**/assets/**/*'
				],
				dest: 'docs/public/assets'
			},
			{
				cwd: 'protected/modules/eyedraw',
				expand: true,
				src: [
					'css/**/*.css',
					'img/**/*',
				],
				dest: 'docs/public/assets/modules/eyedraw'
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