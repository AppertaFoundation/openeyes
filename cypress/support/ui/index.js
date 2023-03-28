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

Cypress.Commands.add('getBySelLike', (selector, ...args) => {
    return cy.get(`[data-test*=${selector}]`, ...args);
});

Cypress.Commands.add('findBySel', {prevSubject: true}, (subject, selector, ...args) => {
    return subject.find(`[data-test=${selector}]`, ...args);
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

    cy.get(`section.element${filterSelector}`).each(($section) => {
        if ($section.find('input[name^="\\[element_dirty\\]"]').val() == '1') {
            cy.wrap($section).within(() => {
                cy.get('.js-remove-element').click();
            });
            cy.get('button.confirm.ok').click();
        } else {
            cy.wrap($section).within(() => { cy.get('.js-remove-element').click(); });
        }
    });
});

Cypress.Commands.add('saveEvent', () => {
    return cy.get('#et_save').click();
});

Cypress.Commands.add('assertEventSaved', (isNewEvent) => {
    if (isNewEvent !== false) {
        return cy.get('#flash-success').contains('created');
    }
    cy.url().should('include', 'view');
});

Cypress.Commands.add('getByDataAttr', (dataAttrName, selector, ...args) => {
    return cy.get(`[data-${dataAttrName}=${selector}]`, ...args);
});

Cypress.Commands.add('getByDataAttrContains', (dataAttrName, selector, ...args) => {
    return cy.get(`[data-${dataAttrName}*=${selector}]`, ...args);
});

Cypress.Commands.add('getByElementName', (selector, ...args) => {
    return cy.get(`[name='${selector}']`, ...args)
});

Cypress.Commands.add('assertElementValue', (dataTest, value, inputType = 'raw') => {
    switch (inputType) {
        case 'input':
            cy.getBySel(dataTest).should('have.value', value);
            break;
        case 'select':
            cy.getBySel(dataTest).get('option:selected').should('have.value', value);
            break;
        case 'select_label':
            cy.getBySel(dataTest).get('option:selected').contains(value);
            break;
        case 'raw':
            cy.getBySel(dataTest).contains(value);
            break;
    }
});

Cypress.Commands.add('assertNoElementValue', (dataTest, value, inputType = 'raw') => {
    cy.get('body').findBySel(dataTest).each(($el) =>
    {
        switch (inputType) {
            case 'input':
                cy.wrap($el).should('not.have.value', value);
                break;
            case 'select':
                cy.wrap($el).get('option:selected').should('not.have.value', value);
                break;
            case 'select_label':
                cy.wrap($el).get('option:selected').should('not.contain', value);
                break;
            case 'raw':
                cy.wrap($el).should('not.contain', value)
                break;
        }
    });
});

Cypress.Commands.add('assertOptionAvailable', (dataTest, value, inputType = 'select') => {
    switch (inputType) {
        case 'select':
            cy.getBySel(dataTest).get(`option[value=${value}]`);
            break;
        case 'select_label':
            cy.wrap($el).contains(value);
            break;
        case 'adder':
            cy.getBySel(dataTest).click();
            cy.assertAdderDialogIncludes(value);
            cy.cancelAdderDialog();
            break;
    }
});

Cypress.Commands.add('assertOptionNotAvailable', (dataTest, value, inputType = 'select') => {
    switch (inputType) {
        case 'select':
            cy.getBySel(dataTest).get(`option[value=${value}]`).should('not.exist');
            break;
        case 'select_label':
            cy.wrap($el).should('not.contain', value);
            break;
        case 'adder':
            cy.getBySel(dataTest).click();
            cy.assertAdderDialogDoesNotInclude(value);
            cy.cancelAdderDialog();
            break;
    }
});
