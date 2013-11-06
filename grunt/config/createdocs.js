module.exports = {
	all: {
		options: {
			tasks: [
				'clean:all',
				'compass:newstyle',
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
				'compass:newstyle',
				'copy:docs',
				'styleguide:dist'
			]
		}
	},
	javascript: {
		options: {
			tasks: [
				'clean:javascript',
				'compass:newstyle',
				'copy:docs',
				'jsdoc:dist'
			]
		}
	}
};