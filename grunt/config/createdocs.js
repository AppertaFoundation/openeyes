module.exports = {
	all: {
		options: {
			tasks: [
				'clean:all',
				'compass:dist',
				'copy:docs',
				'styleguide:dist',
				'jsdoc:dist'
			]
		}
	},
	styleguide: {
		options: {
			tasks: [
				'clean:styleguide',
				'compass:dist',
				'copy:docs',
				'styleguide:dist'
			]
		}
	},
	javascript: {
		options: {
			tasks: [
				'clean:javascript',
				'compass:dist',
				'copy:docs',
				'jsdoc:dist'
			]
		}
	}
};