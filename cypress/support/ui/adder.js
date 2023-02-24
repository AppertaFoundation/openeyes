Cypress.Commands.add('selectAdderDialogOptionText', (string) => {
    cy.get('div.oe-add-select-search :visible').contains(string).click()
})

Cypress.Commands.add('selectAdderDialogOptionAdderID', (headingId, string) => {
    cy.get('[data-adder-id="' + headingId + '"]').contains(string).click()
})

Cypress.Commands.add('selectAdderDialogOptionIDHeading', (idheading, subheading, string) => {
    cy.get('#' + idheading)
        .find('[data-adder-id="' + subheading + '"]')
        .contains(string)
        .click()
})

Cypress.Commands.add('selectAdderDialogOptionClassID', (classField, id, text) => {
    cy.get('[class="' + classField + '"')
        .find('#' + id)
        .contains(text)
        .click()
})

Cypress.Commands.add('selectAdderDialogOptionVariable', (variable, text) => {
    cy.get(variable).contains(text).click()
})

Cypress.Commands.add('confirmAdderDialog', () => {
    cy.get('div.oe-add-select-search > div.add-icon-btn:visible').contains('Click to add').click()
})