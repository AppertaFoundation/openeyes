/// <reference types="cypress" />

declare namespace Cypress {
    interface Chainable<Subject> {
        /**
         * Asserts that an adder dialog includes a value
         * 
         * @param text
         */
        assertAdderDialogIncludes(text: string): Chainable<any>
        
        /**
         * Asserts that an adder dialog does not include a value
         * 
         * @param text
         */
        assertAdderDialogDoesNotInclude(text: string): Chainable<any>

        /**
         * Selects options from the adder dialog menu based on text
         *
         * @param text
         */
        selectAdderDialogOptionText(text: string): Chainable<any>

        /**
         * Selects an adder dialog based on heading data-adder-id and then text
         *
         * @param headingId
         * @param text
         */
        selectAdderDialogOptionAdderID(headingId: string, text: string): Chainable<any>

        /**
         * Selects an adder dialog based on a specific variable and then text
         * @param variable
         * @param text
         */
        selectAdderDialogOptionVariable(variable: string, text: string): Chainable<any>

        /**
         * Confirms an adder dialog by clicking the 'Click to add' button
         */
        confirmAdderDialog(): Chainable<any>

        /**
         * Cancels an adder dialog by clicking the x button
         */
        cancelAdderDialog(): Chainable<any>
    }
  }