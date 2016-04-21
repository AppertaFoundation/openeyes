module.exports = {
	devFile: 'protected/assets/components/modernizr/modernizr.js',
	outputFile: 'protected/assets/js/modernizr.custom.js',
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
	tests: [
		'forms-formattribute'
	],
	uglify: true,
	parseFiles: false
};
