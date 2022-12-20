Cypress.Commands.add('addExaminationElement', (elementName) => {
    cy.get('#js-manage-elements-btn').click();
    const kebabCaseElementName = elementName.replace(/ /g, '-');
    cy.get(`#manage-elements-${kebabCaseElementName}`).click();
    cy.get('#manage-elements-nav .close-icon-btn button').click();
    cy.getElementByName(elementName).should('be.visible');
});
