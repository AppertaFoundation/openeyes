module.exports = {
	src: [
		'protected/modules/*/Gruntfile.js'
	],
	default: {
		src: '<%= hub.src %>',
		tasks: [
			'default'
		]
	},
	build: {
		src: '<%= hub.src %>',
		tasks: [
			'build'
		]
	},
	watch: {
		src: '<%= hub.src %>',
		tasks: [
			'watch'
		]
	}
};