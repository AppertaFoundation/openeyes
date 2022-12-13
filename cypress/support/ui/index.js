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
