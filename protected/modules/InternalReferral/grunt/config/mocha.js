module.exports = function(grunt) {
	return {
		test: {
			src: [
				'tests/js/runners/*.html'
			],
			options: {
				run: true,
				reporter: 'Spec'
			}
		}
	};
};