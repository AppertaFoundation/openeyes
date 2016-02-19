module.exports = {
	options: {
		run: true,
		reporter: 'Spec',
		log: true
	},
	all: {
		src: [
			'protected/tests/js/runners/**/*.html',
			'protected/modules/**/tests/js/runners/**/*.html',
			'!protected/modules/eyedraw/tests/js/runners/index.html'
		]
	},
	core: {
		src: [
			'protected/tests/js/runners/*.html',
		]
	},
	singleTest: {
		src: [
			'protected/tests/js/runners/*.html',
		],
		options: {
			mocha: {
				grep: 'Example'
			}
		}
	}
};