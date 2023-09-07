const { defineConfig } = require("cypress");
const { cloudPlugin } = require("cypress-cloud/plugin");
const del = require('del');

module.exports = defineConfig({
  reporter: 'dot',
  e2e: {
    baseUrl: 'http://localhost',
    viewportWidth: 1280,
    viewportHeight: 737,
    setupNodeEvents(on, config) {

      on('after:spec', (spec, results) => {
        if (results && results.video) {
          // Do we have failures for any retry attempts?
          const failures = results.tests.some((test) =>
            test.attempts.some((attempt) => attempt.state === 'failed')
          )
          if (!failures) {
            // delete the video if the spec passed and no tests retried
            fs.unlinkSync(results.video)
          }
        }
      })

      return cloudPlugin(on, config);
    },
    defaultCommandTimeout: 7000,
    retries: {
      // Configure retry attempts for `cypress run`
      // Default is 0
      runMode: 2,
      // Configure retry attempts for `cypress open`
      // Default is 0
      openMode: 0
    }
  },
});
