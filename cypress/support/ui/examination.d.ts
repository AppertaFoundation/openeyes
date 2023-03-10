/// <reference types="cypress" />

declare namespace Cypress {
    interface Chainable<Subject> {
        /**
         * pass a single string to add an element by name
         * pass an array of strings to add multiple elements by name
         *
         * @param elementNames
         */
        addExaminationElement(elementNames: string|Array): Chainable<any>;
    }
}
