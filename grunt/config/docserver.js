module.exports = {
	options: {
		port: 9002,
	},
	all: {
		options: {
			base: 'docs/public/'
		}
	},
	styleguide: {
		options: {
			base: 'docs/public',
			url: '/styleguide'
		}
	},
	javascript: {
		options: {
			base: 'docs/public',
			url: '/jsdoc'
		}
	}
};