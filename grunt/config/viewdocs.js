module.exports = {
	all: {
		options: {
			tasks: [
				'docs:all',
				'docserver:all'
			]
		}
	},
	styleguide: {
		options: {
			tasks: [
				'docs:styleguide',
				'docserver:styleguide'
			]
		}
	},
	javascript: {
		options: {
			tasks: [
				'docs:javascript',
				'docserver:javascript'
			]
		}
	}
};