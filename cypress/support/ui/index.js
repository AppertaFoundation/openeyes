import './adder'
import './consent'
import './examination'
import './eyedraw'
import './operationnote'

Cypress.Commands.add('getElementByName', (elementName) => {
    return cy.get(`section[data-element-type-name="${elementName}"]`);
});

Cypress.Commands.add('getElementSideByName', (elementName, side) => {
    return cy.getElementByName(elementName).find(`.element-eyes .column[data-side=${side}]`);
});


Cypress.Commands.add('getBySel', (selector, ...args) => {
    return cy.get(`[data-test=${selector}]`, ...args);
});

Cypress.Commands.add('removeElementSide', (elementName, side) => {
    return cy.getElementSideByName(elementName, side).find('.remove-side').click();
});

Cypress.Commands.add('removeElements', (exceptElementNames) => {
    if (exceptElementNames === undefined) {
        exceptElementNames = [];
    }
    if (!Array.isArray(exceptElementNames)) {
        exceptElementNames = [exceptElementNames];
    }

    const filterSelector = exceptElementNames
        .map((elementName) => {
            return `[data-element-type-name!="${elementName}"]`;
        })
        .join('');

    cy.get(`section.element${filterSelector} .js-remove-element`).click({ multiple: true });
});

Cypress.Commands.add('saveEvent', () => {
    return cy.get('#et_save').click();
});

/**
 * Checks for flash message to verify success through the UI
 */
Cypress.Commands.add('assertEventSaved', (newEvent) => {
    if (newEvent === undefined) {
        newEvent = true;
    }
    return cy.get('#flash-success').contains(newEvent ? 'created' : 'updated');
});
