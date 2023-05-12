/// <reference types="cypress" />

declare namespace Cypress {
    interface Chainable<Subject> {
        /**
         * visit the worklist page
         *
         */
        visitWorklist(): Chainable<AUTWindow>;
        /**
         * Selects worklist patient element on the worklist screen and 
         * scroll to that element
         *
         */
        getWorklist(worklistId: number): Chainable<any>;
        /**
         * Selects the Arrived filter element on the worklist screen
         *
         */
        getWorklistArrivedFilter(): Chainable<any>;
        /**
         * Selects the Arrived filter count element on the worklist screen
         *
         */
        getWorklistArrivedFilterCount(): Chainable<any>;
        /**
         * Opens the Navigation bar on the worklist screen
         *
         */
        openWorklistNavBar(): void; 
        /**
         * Hides the Navigation bar on the worklist screen if it is open
         *
         */
        hideWorklistNavBar(): void;
    }
}
