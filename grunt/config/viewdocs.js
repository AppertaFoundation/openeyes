module.exports = {
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