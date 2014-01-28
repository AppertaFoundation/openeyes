module.exports = {
	docs: {
		files: [
			{
				src: ['protected/assets/css/**/*'],
				dest: 'docs/public/assets/core/'
			},
			{
				src: ['protected/assets/img/**/*'],
				dest: 'docs/public/assets/core/'
			},
			{
				src: ['protected/assets/js/**/*'],
				dest: 'docs/public/assets/core/'
			},
			{
				cwd: 'docs/src/',
				expand: true,
				src: ['components/**/*'],
				dest: 'docs/public/'
			},
			{
				cwd: 'docs/src/',
				expand: true,
				src: ['fragments/**/*'],
				dest: 'docs/public/'
			},
			{
				cwd: 'docs/src/',
				expand: true,
				src: ['static-templates/**/*'],
				dest: 'docs/public/'
			},
			{
				cwd: 'docs/src/',
				expand: true,
				src: ['js/**/*'],
				dest: 'docs/public/assets'
			},
			{
				cwd: 'docs/src/',
				expand: true,
				src: ['css/**/*'],
				dest: 'docs/public/assets'
			},
			{
				cwd: 'docs/src/',
				expand: true,
				src: ['index.php'],
				dest: 'docs/public/'
			}
		]
	}
};