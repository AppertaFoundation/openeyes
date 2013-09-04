module.exports = {
	all: {
		options: {
			tasks: [
				'createdocs:all',
				'docserver:all'
			]
		}
	},
	styleguide: {
		options: {
			tasks: [
				'createdocs:styleguide',
				'docserver:styleguide'
			]
		}
	},
	javascript: {
		options: {
			tasks: [
				'createdocs:javascript',
				'docserver:javascript'
			]
		}
	}
};