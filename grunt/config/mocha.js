module.exports = {
	options: {
		run: true,
		reporter: 'Spec',
		src: [
			'protected/assets/js/specs/runner/*.html',
		]
	},
	all: {
		src: [
			'protected/assets/js/specs/runner/*.html',
			'protected/modules/**/assets/js/specs/runner/*.html'
		]
	},
	core: {
		src: [
			'protected/assets/js/specs/runner/*.html',
		]
	},
	singleTest: {
		src: [
			'protected/assets/js/specs/runner/*.html',
		],
		options: {
			mocha: {
				grep: 'Example'
			}
		}
	}
};