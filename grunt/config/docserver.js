module.exports = {
	options: {
		port: 9002,
		hostname: '0.0.0.0'
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