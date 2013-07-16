var connect = require('connect');
var path = require('path');

// Spawn a new child process to open up the docs in a new tab in chrome.
function openInChrome(grunt, options) {

  var cp = require('child_process').spawn('open', [
    '-a',
    '/Applications/Google Chrome.app',
    'http://' + options.hostname + ':' + options.port
  ]);

  cp.stderr.on('data', function (data) {
    grunt.log.error(data.toString('utf8'));
    grunt.log.writeln(
      'Unable to open chrome. Please open http://' + 
      options.hostname + ':' + options.port + 
      ' in your browser to view the docs.'
    );
  });
}

module.exports = function(grunt) {

  return function() {

    this.async();

    var options = this.options({
      port: 9001,
      hostname: 'localhost',
      base: '.'
    });
    
    var base = path.resolve(options.base);

    var server = connect(
        connect.static(base),    // Serve static files.
        connect.directory(base)  // Make empty directories browsable.
      )
      .listen(options.port, options.hostname)
      .on('listening', function() {
        var address = server.address();
        grunt.log.writeln('Started connect web server on ' + (address.host || 'localhost') + ':' + address.port + '.');
        openInChrome(grunt, options);
      })
      .on('error', function(err) {
        if (err.code === 'EADDRINUSE') {
          grunt.fatal('Port ' + options.port + ' is already in use by another process.');
        } else {
          grunt.fatal(err);
        }
      });    
   };
};