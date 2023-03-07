/// <reference types="cypress" />

declare namespace Cypress {
    interface Chainable<Subject> {
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
         * Selects an adder dialog based on id then data-adder-id and then string
         * @param id
         * @param subheadingId
         * @param text
         */
        selectAdderDialogOptionIDHeading(id: string, subheadingId: string, text): Chainable<any>
        /**
         * Selects an adder dialog based on class then id and then string
         * @param classField
         * @param id
         * @param text
         */
        selectAdderDialogOptionClassID(classField: string, id: string, text: string): Chainable<any>
        /**
         * Selects an adder dialog based on a specific variable and then text
         * @param variable
         * @param text
         */
        selectAdderDialogOptionVariable(variable: string, text: string): Chainable<any>
        /**
         * Clicks confirm adder dialog button
         */
        confirmAdderDialog(): Chainable<any>
    }
  }