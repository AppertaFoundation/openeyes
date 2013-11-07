module.exports = {
	dist : {
		src: ['js/util.js', 'js/dialogs.js'],
		options: {
			destination: 'docs/public/jsdoc',
			template: 'docs/templates/jsdoc',
			tutorials: 'docs/src/tutorials/javascript',
			recurse: true
		}
	}
};