module.exports = function(grunt) {
	return {
		sass: {
			files: 'assets/sass/**/*.scss',
			tasks: ['compass:dist']
		}
	};
};