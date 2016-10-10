module.exports = function(grunt) {
	return {
		browser: {
			options: {
				mode: 'browser'
			}
		},
		headless: {
			options: {
				mode: 'headless'
			}
		}
	};
};