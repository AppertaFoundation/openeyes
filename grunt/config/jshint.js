module.exports = {
	files: [
		'*.js',
		'*.json',
		'grunt/**/*.js',
		'protected/assets/js/OpenEyes*',
		'docs/templates/**/js/script.js',
		'protected/modules/**/assets/js/**/*.js',
		'!protected/modules/**/assets/js/**/*.min.js'
	],
	options: {
		jshintrc: ".jshintrc",
		reporter: require('jshint-stylish')
	}
};