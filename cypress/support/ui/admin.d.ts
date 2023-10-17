/// <reference types="cypress" />

declare namespace Cypress {
    interface Chainable<Subject> {
        /**
         * Drag and drop the row
         * Use element.getBoundingClientRect() to get the coordinates
         *
         * @param text
         */
        dragAndDropRow(rowIndex: int, x: int, y: int): void
    }
}