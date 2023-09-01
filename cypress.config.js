const { defineConfig } = require("cypress");
const del = require('del');

module.exports = defineConfig({
  reporter: 'dot',
  e2e: {
    baseUrl: 'http://localhost',
    viewportWidth: 1280,
    viewportHeight: 737,
    setupNodeEvents(on, config) {
      on('after:spec', (spec, results) => {
        if (results && results.stats.failures === 0 && results.video) {
          // delete the video if test is successful and there is a video
          // This saves a lot of time as otherwise the video is compressed then uploaded to the dashboard
          return del(results.video)
        }
      })
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
