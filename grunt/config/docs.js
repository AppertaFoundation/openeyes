module.exports = {
	all: {
		options: {
			tasks: [
				'clean:docs',
				'build',
				'copy:docs',
				'styleguide:dist',
				'jsdoc:dist'
			]
		}
	},
	javascript: {
		options: {
			tasks: [
				'clean:docs',
				'build',
				'copy:docs',
				'jsdoc:dist'
			]
		}
	},
	css: {
		options: {
			tasks: [
				'clean:docs',
				'build',
				'copy:docs',
				'styleguide:dist'
			]
		}
	}
};