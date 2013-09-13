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
	compile: {
		src: '<%= hub.options.src %>',
		tasks: [
			'compile'
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