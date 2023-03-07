Cypress.Commands.add('getWorklist', (worklistId) => {
    // To scroll to the newly added worklist item
    cy.getBySel(`js-worklist-${worklistId}`).scrollIntoView();
    return cy.getBySel(`js-worklist-${worklistId}`);
});

Cypress.Commands.add('getWorklistArrivedFilter', () => {
    return cy.getBySel('clinic-filter');
});

Cypress.Commands.add('getWorklistArrivedFilterCount', () => {
    return cy.getBySel('clinic-filter-count');
});