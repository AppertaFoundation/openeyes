/**
 * Selects options from the adder dialog menu based on text
 */
Cypress.Commands.add('selectAdderDialogOptionText', (string) => {
    cy.contains('div.oe-add-select-search :visible', string).click()
})

/**
 * Selects an adder dialog based on data-adder-id and then text
 */
Cypress.Commands.add('selectAdderDialogOptionHeading', (heading, string) => {
    cy.get('[data-adder-id="' + heading + '"]').contains(string).click()
})

/**
 * Selects an adder dialog based on id then data-adder-id and then string
 */
Cypress.Commands.add('selectAdderDialogOptionIDHeading', (idheading, subheading, string) => {
    cy.get('#' + idheading)
        .find('[data-adder-id="' + subheading + '"]')
        .contains(string)
        .click()
})

/**
 * Selects an adder dialog based on class then id and then string
 */
Cypress.Commands.add('selectAdderDialogOptionClassID', (classField, id, string) => {
    cy.get('[class="' + classField + '"')
        .find('#' + id)
        .contains(string)
        .click()
})

/**
 * Selects an adder dialog based on a specific variable and then text
 */
Cypress.Commands.add('selectAdderDialogOptionVariable', (variable, string) => {
    cy.get(variable).contains(string).click()
})

/**
 * Clicks confirm adder dialog button
 */
Cypress.Commands.add('confirmAdderDialog', () => {
    cy.get('div.oe-add-select-search > div.add-icon-btn:visible').contains('Click to add').click()
})