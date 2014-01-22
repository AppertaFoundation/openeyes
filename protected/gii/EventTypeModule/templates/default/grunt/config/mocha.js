module.exports = function(grunt) {
	return {
		test: {
			src: [
				'assets/js/specs/runner/*.html'
			],
			options: {
				run: true,
				reporter: 'Spec'
			}
		}
	};
};