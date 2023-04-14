const { defineConfig } = require("cypress");
// need to install the "del" module as a dependency
// npm i del --save-dev
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
          // `del()` returns a promise, so it's important to return it to ensure
          // deleting the video is finished before moving on
          del(results.video)
        }
      })
    },
    defaultCommandTimeout: 7000
  },
});
