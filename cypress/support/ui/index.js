import './adder'
import './consent'
import './examination'
import './eyedraw'
import './operationnote'
import './worklist'
import './admin/queue-sets'

Cypress.Commands.add('getElementByName', (elementName) => {
    return cy.get(`section[data-element-type-name="${elementName}"]`);
});

Cypress.Commands.add('getElementSideByName', (elementName, side) => {
    return cy.getElementByName(elementName).find(`.element-eyes .column[data-side=${side}]`);
});

Cypress.Commands.add('getBySel', (dataTest, additionalSelectors = "", ...args) => {
    return cy.get(`[data-test="${dataTest}"]${additionalSelectors}`, ...args);
});

Cypress.Commands.add('getBySelLike', (dataTest, additionalSelectors = "", ...args) => {
    return cy.get(`[data-test*="${dataTest}"]${additionalSelectors}`, ...args);
});

Cypress.Commands.add('findBySel', {prevSubject: true}, (subject, selector, ...args) => {
    return subject.find(`[data-test="${selector}"]`, ...args);
});

Cypress.Commands.add('removeElementSide', (elementName, side) => {
    return cy.getElementSideByName(elementName, side).find('.remove-side').click();
});

Cypress.Commands.add('removeElements', (exceptElementNames, force = false) => {

    if (exceptElementNames === undefined) {
        exceptElementNames = [];
    }
    if (!Array.isArray(exceptElementNames)) {
        exceptElementNames = [exceptElementNames];
    }

    cy.get(`section.element`).each(($section) => {
        const elementTypeName = $section.attr('data-element-type-name');
        if (exceptElementNames.includes(elementTypeName)) {
            cy.log(`Skip removing element ${elementTypeName}`);
            return; // Skip this element
        }

        cy.removeElement(elementTypeName, force);
    });
});

Cypress.Commands.add('removeElement', (elementName, force = false) => {

    cy.get(`section.element[data-element-type-name="${elementName}"]`).each(($section) => {
        const $elementActions = $section.find('.element-actions');

        // If force is set, remove elements even if they are set to mandatory or the user does not have permission to remove
        // This is done by adding the missing js-remove-element class to the trash icon
        if (force) {
            // Remove elements with class "no-permissions" set on the trash icon
            $elementActions.find('.no-permissions')
                .removeClass('no-permissions')
                .addClass('js-remove-element');

            // Remove mandatory elements
            $elementActions.find('span.disabled.js-has-tooltip')
                .removeClass('disabled')
                .removeClass('js-has-tooltip')
                .addClass('js-remove-element');
        }

        if ($section.find('input[name*="element_dirty"]').val() == '1') {
            const removeElement = $section.find('.js-remove-element');
            if (removeElement.length > 0) {
                cy.wrap($section).within(() => {
                    cy.wrap(removeElement).click();
                });
                cy.get('button.confirm.ok').click();
            }
        } else {
            const removeElement = $section.find('.js-remove-element');
            if (removeElement.length) {
                cy.wrap($section).within(() => { cy.get('.js-remove-element').click(); });
            }
        }
    });
});

Cypress.Commands.add('saveEvent', () => {
    return cy.get('#et_save').click();
});

Cypress.Commands.add('cancelEvent', () => {
    return cy.get('[data-test="event-action-cancel"]:visible').first().click();
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

Cypress.Commands.add('fillAndSelectAutocomplete', (searchValue) => {
    cy.getBySel('oe-autocompletesearch').clear().type(searchValue);
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

Cypress.Commands.add('generateRandomString', (stringLength) => {
    let randomString = '';
    let randomAscii;

    for (let index = 0; index < stringLength; index++) {
        randomAscii = Cypress._.random(97, 122);
        randomString += String.fromCharCode(randomAscii);
    }

    return randomString;
});