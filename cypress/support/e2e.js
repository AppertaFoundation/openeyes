// ***********************************************************
// This example support/e2e.js is processed and
// loaded automatically before your test files.
//
// This is a great place to put global configuration and
// behavior that modifies Cypress.
//
// You can change the location of this file or turn off
// automatically serving support files with the
// 'supportFile' configuration option.
//
// You can read more here:
// https://on.cypress.io/configuration
// ***********************************************************

// Import commands.js using ES2015 syntax:
// import './commands'
import './yii-commands'
import './ui/index'
import './ui/worklist'
import './ui/admin'
import chaiString from 'chai-string'
import "cypress-cloud/support";

// Alternatively you can use CommonJS syntax:
// require('./commands')

require("cypress-cloud/support");

// Options for log collector
const options = {
    enableExtendedCollector: true,
};

// Register the log collector
require("cypress-terminal-report/src/installLogsCollector")(options);

before(() => {
    // ensure assets are not cached in the browser between test runs
    Cypress.automation('remote:debugger:protocol', {
        command: 'Network.clearBrowserCache'
    });

    chai.use(chaiString);
});
