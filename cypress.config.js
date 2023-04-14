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
    defaultCommandTimeout: 7000
  },
});
