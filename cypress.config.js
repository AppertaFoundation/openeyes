const { defineConfig } = require("cypress");

module.exports = defineConfig({
  e2e: {
    baseUrl: 'http://localhost',
    viewportWidth: 1280,
    viewportHeight: 737,
    setupNodeEvents(on, config) {
      // implement node event listeners here
    },
  },
});
