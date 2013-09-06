module.exports = {
	dist: {
		options: {
			framework: {
				name: 'kss'
			},
			template: {
				src: 'docs/templates/styleguide',
				include: ''
			}
		},
		files: {
			'docs/public/styleguide': 'sass/new/*.scss'
		}
	}
};