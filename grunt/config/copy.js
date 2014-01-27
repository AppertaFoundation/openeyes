module.exports = {
	docs: {
		files: [
			{
				cwd:'protected/assets',
				expand: true,
				src: ['css/**/*.css'],
				dest: 'docs/public/assets'
			},
			{
				cwd:'protected/assets',
				expand: true,
				src: ['img/**/*'],
				dest: 'docs/public/assets'
			},
			{
				cwd:'protected/assets',
				expand: true,
				src: ['js/**/*.js'],
				dest: 'docs/public/assets'
			},
			{
				cwd:'protected/assets',
				expand: true,
				src: ['components/**/*'],
				dest: 'docs/public/assets'
			},
			{
				cwd: 'protected',
				expand: true,
				src: ['modules/**/assets/**/*'],
				dest: 'docs/public/assets'
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