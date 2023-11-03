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

        /**
         * pass a boolean to determine if manual pin signing is required
         * to test Prescription from MM and to check if the Prescriber signature is signed
         * and if the MM switches to draft prescription the signature should be removed and when editing
         * and signing the signature should be present
         *
         * @param pinSigningRequired
         */
        addPrescriptionFromMMThenSaveAsDraftAndSignAgain(pinSigningRequired: boolean): Chainable<any>;
    }
}
