module.exports = function(grunt) {

	/* Set the config */
	grunt.initConfig(require('./grunt/config')(grunt));

	/* Load the npm grunt tasks */
	require('load-grunt-tasks')(grunt, 'grunt-*');

	/* Load our custom grunt tasks */
	grunt.loadTasks('./grunt/tasks');

	var _ = grunt.util._;


	grunt.registerTask(
		'expose', "Expose available tasks as JSON object.", function () {
			var tasks = grunt.task._tasks;
			_.each( tasks, function( value, key, list ) {
				var targets = grunt.config.getRaw( key ) || {};
				delete targets['options'];
				list[ key ].targets = Object.keys(targets);
			});
			grunt.log.write("EXPOSE_BEGIN" + JSON.stringify(tasks, '', 2) + "EXPOSE_END");
		}
	);

	grunt.registerTask('hello', 'test', function() {

		var done = this.async();

 		var child = grunt.util.spawn({
      cmd: 'node',
      args: [ 'test.js' ]
    }, function (err, result, code) {
      done();
    });
    child.stdout.pipe(process.stdout);
    child.stderr.pipe(process.stderr);
	})
};