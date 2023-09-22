const { defineConfig } = require("cypress");
const { cloudPlugin } = require("cypress-cloud/plugin");
const { debuggerPlugin } = require('cypress-debugger');

module.exports = defineConfig({
  e2e: {
    baseUrl: 'http://localhost',
    viewportWidth: 1280,
    viewportHeight: 737,
    setupNodeEvents(on, config) {

      require('cypress-terminal-report/src/installLogsPrinter')(on);

      cloudPlugin(on, config);

      return config;
    },
    videoUploadOnPasses: false,
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
