module.exports = {
	dist : {
		src: [
			'js/OpenEyes*',
			'docs/src/jsdoc/README.md'
		],
		options: {
			destination: 'docs/public/jsdoc',
			template: 'docs/templates/jsdoc',
			tutorials: 'docs/src/tutorials/javascript',
			recurse: true
		}
	}
};