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
         * Selects options from the adder dialog menu based on data-label attribute with the provided value
         *
         * @param text
         */
        selectAdderDialogOptionByDataLabel(text: string): Chainable<any>

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

        /**
         * Function to check if the correct dialog columns are shown when opening adderDialog and after selecting values
         * Column headers and bodies share the same id, but in different data attributes
         * Header is using data-id and options are using data-adder-id with the same value, that is why we need only one id
         * to check both places
         * @param elementId
         * @param adderButtonSelector
         * @param visibleColumnIds
         * @param hiddenColumnIds
         * @param toSelectOptionsText
         * @param visibleColumnIdsAfterSelect
         * @param hiddenColumnIdsAfterSelect
         * @param side
         */
        dialogCorrectOptionsShownOnOpenAndAfterOptionsSelected(elementId: string,
                                                               adderButtonSelector: string,
                                                               visibleColumnIds: Array<string>,
                                                               hiddenColumnIds: Array<string>,
                                                               toSelectOptionsText: Array<string>,
                                                               visibleColumnIdsAfterSelect: Array<string>,
                                                               hiddenColumnIdsAfterSelect: Array<string>,
                                                               side: string): Chainable<any>

        /**
         * Asserts visible adderDialog correct columns are shown and hidden
         * @param visibleColumnIds
         * @param hiddenColumnIds
         */
        assertVisibleAndHiddenColumnsBasedById(visibleColumnIds: Array<string>,
                                               hiddenColumnIds: Array<string>): Chainable<any>
    }
}