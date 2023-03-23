/**
 * A Series of commands for working with adder dialogs.
 */

Cypress.Commands.add('assertAdderDialogIncludes', (text) => {
    cy.get('[data-test="adder-dialog"]:visible').should('contain', text)
})

Cypress.Commands.add('assertAdderDialogDoesNotInclude', (text) => {
    cy.get('[data-test="adder-dialog"]:visible').should('not.contain', text)
})

Cypress.Commands.add('selectAdderDialogOptionText', (text) => {
    cy.get('[data-test="adder-dialog"] :visible').contains(text).click()
})

Cypress.Commands.add('selectAdderDialogOptionAdderID', (headingId, text) => {
    cy.get('[data-adder-id="' + headingId + '"]').contains(text).click()
})

Cypress.Commands.add('confirmAdderDialog', () => {
    cy.get('div[data-test="adder-dialog"]:visible > div.add-icon-btn').contains('Click to add').click({force: true})
})

Cypress.Commands.add('cancelAdderDialog', () => {
    cy.get('div[data-test="adder-dialog"]:visible > div.close-icon-btn').click({force: true})
})