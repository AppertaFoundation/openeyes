module.exports = {
	devFile: 'js/components/modernizr/modernizr.js',
	outputFile: 'js/vendor/modernizr.js',
	extra: {
		shiv: false,
		printshiv: false,
		load: false,
		mq: false,
		cssclasses: true
	},
	extensibility: {
		addtest: false,
		prefixed: false,
		teststyles: false,
		testprops: false,
		testallprops: false,
		hasevents: false,
		prefixes: false,
		domprefixes: false
	},
	customTests: [
		'grunt/config/modernizr/tests/*.js'
	],
	uglify: true,
	parseFiles: false
};