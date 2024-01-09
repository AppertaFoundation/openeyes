/// <reference types="cypress" />

declare namespace Cypress {
    interface Chainable<Subject> {
        /**
         * Cypress selector for an OE Event element in the UI
         *
         * @param name
         */
        getElementByName(name: string): Chainable<any>
        /**
         * Cypress selector for a sided OE Event element in the UI
         * @param elementName
         * @param side "left" | "right"
         */
        getElementSideByName(elementName: string, side: string): Chainable<any>
        /**
         * Cypress selector shortcut for finding a dom element based on the data-test attribute value as the selector
         * @param selector
         * @param additionalSelectors
         */
        getBySel(selector: string, additionalSelectors: string): Chainable<any>
        /**
         * Cypress selector shortcut for finding a dom element based on partial data-test attribute value as the selector
         * @param selector
         * @param additionalSelectors
         */
        getBySelLike(selector: string, additionalSelectors: string): Chainable<any>
        /**
         * UI shortcut to remove the side of the named OE Event element
         * @param elementName
         * @param side
         */
        removeElementSide(elementName: string, side: string): Chainable<any>
        /**
         * Removes all elements from an event. Useful for resetting an event to a known state
         * @param exceptElementNames name(s) of any elements to KEEP
         * @param force if true will remove the elements even if they are mandatory, dirty or disabled
         */
        removeElements(exceptElementNames: string | string[], force: boolean): Chainable<any>
        /**
         * Removes a single element with the given name
         * @param elementName name of the element to remove
         * @param force if true will remove the element even if it is mandatory, dirty or disabled
         */
        removeElement(elementName: string, force: boolean): Chainable<any>
        /**
         * click the save button on the current OE Event form
         */
        saveEvent(): Chainable<any>
        /**
         * click the cancel button on the current OE Event form
         */
        cancelEvent(): Chainable<any>
        /**
         * Look for the event saved confirmation message. Checks if is created or updated based on the isNewEvent flag
         * @param isNewEvent defaults to true
         */
        assertEventSaved(isNewEvent: ?boolean): Chainable<any>
        /**
         * Type the given value into the autocomplete search box and select the first value that matches that from
         * the rendered dropdown list
         *
         * @param searchValue
         */
        fillAndSelectAutocomplete(searchValue: string): void
        /**
         * Cypress selector short cut for any data attribute equals selector
         * @param getByDataAttr
         * @param selector
         */
        getByDataAttr(getByDataAttr: string, selector: string): Chainable<any>
        /**
         * Cypress selector short cut for any data attribute contains selector
         * @param getByDataAttr
         * @param selector
         */
        getByDataAttrContains(getByDataAttr: string, selector: string): Chainable<any>
        /**
         * Cypress selector short cut for any element with name attribute
         * @param selector
         */
        getByElementName(selector: string): Chainable<any>

        /**
         * Returns an element when it exists in the dom
         *
         * Usage:
         * cy.getElementIfExists('div#my-element')
         *   .then((ele) => {
         *     if(!ele){
         *       return;
         *     }
         *    // your code here
         * })
         *
         * @param selector The selector to check for.
         */
        getElementIfExists(selector: string): Chainable<any>
    }
  }