Cypress.Commands.add('addExaminationElement', (elementNames) => {
    if (!Array.isArray(elementNames)) {
        elementNames = [elementNames];
    }

    cy.get('#js-manage-elements-btn').click();
    elementNames.forEach((elementName) => {
        const kebabCaseElementName = elementName.replace(/ /g, '-');
        cy.get(`#manage-elements-${kebabCaseElementName}`).within((button) => {
            if (!button.hasClass('added') && !button.hasClass('mandatory')) {
                button.click();
                // Wait for the element to be added to the page
                cy.intercept({
                    method: 'GET',
                    url: '/OphCiExamination/Default/ElementForm*'
                }).as('ElementForm');
                cy.wait('@ElementForm');
            }
        });
    });

    cy.get('#manage-elements-nav .close-icon-btn button').click();
    elementNames.forEach((elementName) => {
        cy.getElementByName(elementName).scrollIntoView().should('be.visible');
    });
});
