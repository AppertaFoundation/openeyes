/**
 * A Series of commands for working with admin pages.
 */
Cypress.Commands.add('dragAndDropRow', (rowIndex, x, y) => {
    cy.get('.sortable>tr').eq(rowIndex).then(row => {
        const coordinates = row[0].getBoundingClientRect();
        cy.wrap(row)
        .trigger('mousedown', { which: 1, pageX: coordinates.x, pageY: coordinates.y })
        .trigger('mousemove', { which: 1, pageX: x + 10, pageY: y + 10 })
        .trigger('mouseup', { which: 1, force: true });
    });
})