module.exports = {
	options: {
		concurrent: 10,
		src: 'protected/modules/*/Gruntfile.js'
	},
	default: {
		src: '<%= hub.options.src %>',
		tasks: [
			'default'
		]
	},
	build: {
		src: '<%= hub.options.src %>',
		tasks: [
			'build'
		]
	},
	watch: {
		src: '<%= hub.options.src %>',
		tasks: [
			'watch'
		]
	}
};