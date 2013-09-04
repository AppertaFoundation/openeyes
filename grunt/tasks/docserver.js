var connect = require('connect');
var http = require('http');

module.exports = function(grunt) {

	grunt.registerMultiTask('docserver', 'Spin up a connect server to display the documentation', function() {

		// Keep this task running indefinitely.
		this.async();

		// Merge the options with default values.
		var options = this.options({
			port: 9001,
			base: '.',
			url: '/',
			hostname: 'localhost'
		});

		// Starts a connect server to display the docs (using static and directory middleware).
		function startServer() {

			var app = connect();
			app.use(connect.static(options.base));
			app.use(connect.directory(options.base));

			var server = http.createServer(app);
			server.listen(options.port, options.hostname);

			server.on('listening', function() {
				grunt.log.writeln('Started docs server on ' + (options.host || 'localhost') + ':' + options.port);
				openInChrome();
			});

			server.on('error', function(err) {
				grunt.fatal(err);
			});
		}

		// Spawn a new child process to open up the docs in a new tab in chrome.
		function openInChrome() {
			grunt.util.spawn({
				cmd: 'open',
				args: [
					'-a',
					'/Applications/Google Chrome.app',
					'http://' + options.hostname + ':' + options.port + options.url
				]
			}, function(error, result){
				if (error) {
					grunt.log.error(error.toString());
					grunt.log.writeln('Unable to open chrome. View http://' + options.hostname + ':' + options.port + options.url + ' in your browser.');
					return;
				}
				grunt.log.write(result);
			});
		}

		startServer();
	});
};