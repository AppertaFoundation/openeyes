/// <reference types="cypress" />

declare namespace Cypress {
    interface Chainable<Subject> {
        /**
         * Fill the Add Queue Set popup
         */
        addQueueSet(options: Record<string, any>): Chainable<any>;
    }
}
