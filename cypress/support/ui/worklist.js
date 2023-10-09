/**
 * This command will visit the worklist screen
 */
Cypress.Commands.add('visitWorklist', () => {
    cy.visit('/worklist/view');
})

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

Cypress.Commands.add('openWorklistNavBar', () => {
    cy.getBySel('nav-worklist-btn').then(($ele) => {
        if (!$ele.hasClass('open')) {
            $ele.click();
        }
    });
});

Cypress.Commands.add('hideWorklistNavBar', () => {
    // Close the navbar if it is open
    cy.getBySel('nav-worklist-btn').then(($ele) => {
        if ($ele.hasClass('open')) {
            $ele.click();
        }
    });
});

Cypress.Commands.add('addPathStep', (trSelector, stepDataAttribute) => {

    cy.get(trSelector).within(tr => {
        cy.getBySel(`patient-checkbox`).click();
    });

    cy.getBySel(`worklist-adder`).should('be.visible').within(adder => {
        cy.getBySel(stepDataAttribute).click();
    });
});
