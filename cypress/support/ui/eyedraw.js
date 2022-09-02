/**
 * A Series of commands for working with the standard Eyedraw UI components.
 */

/**
 * Work with the standard EyeDraw UI to add a doodle to a DOM element by name
 * If the doodle button is in the more drawer, doodleIsInDrawer must be true
 */
Cypress.Commands.add('addEyedrawDoodle', (element, doodleName, doodleIsInDrawer) => {
    if (doodleIsInDrawer) {
        cy.wrap(element.find('.ed-button')).contains(doodleName)
            .parents('.ed2-toolbar-panel-drawer').parent('li').find('.ed-button-more').click();    
    }
    
    return cy.wrap(element.find('.ed-button')).contains(doodleName).parent('a').click();
});

/**
 * Wrapper for addEyedrawDoodle to pass in an element name and side to define where 
 * the eyedraw doodle should be added.
 */
Cypress.Commands.add('addEyedrawDoodleInElement', (elementName, doodleName, side, doodleIsInDrawer) => {
    if (side !== undefined) {
        return cy.getElementSideByName(elementName, side)
            .then((element) => {
                return cy.addEyedrawDoodle(element, doodleName, doodleIsInDrawer);
            });
    } 
    
    return cy.getElementByName(elementName)
        .then((element) => {
            return cy.addEyedrawDoodle(element, doodleName, doodleIsInDrawer);
        });
});

/**
 * Remove the given doodle by name from the DOM element.
 * Will only remove the first that it finds of that name.
 */
Cypress.Commands.add('removeEyedrawDoodle', (element, doodleName) => {
    cy.wrap(element.find('select.ed2-selected-doodle-select')).select(doodleName);
    cy.wrap(element.find('.ed2-doodle-popup-toolbar .ed-button[data-function="deleteSelectedDoodle"]')).click();
});

/**
 * Wrapper for removeEyedrawDoodle to pass in an element name and side for where the doodle 
 * should be removed from.
 */
Cypress.Commands.add('removeEyedrawDoodleInElement', (elementName, doodleName, side) => {
    if (side !== undefined) {
        return cy.getElementSideByName(elementName, side)
            .then((element) => {
                return cy.removeEyedrawDoodle(element, doodleName);
            });
    }

    
    return cy.getElementByName(elementName)
        .then((element) => {
            return cy.removeEyedrawDoodle(element, doodleName);
        });
});