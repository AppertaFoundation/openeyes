module.exports = {
	all: {
		options: {
			tasks: [
				'clean:docs',
				'compass:dist',
				'copy:docs',
				'styleguide:dist',
				'jsdoc:dist'
			]
		}
	}
};