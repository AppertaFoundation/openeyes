describe('Check Pharmacy worklist access rules', () => {
    const worklist_url = '/OphDrPrescription/OphDrPrescriptionPharmacyWorklist/default/index/';

    context('User with rights can access the Pharmacy worklist', () => {
            before(() => {
                cy.login();

                cy.createUser(['User', 'OprnViewPharmacyWorklist']).then(user => {
                    cy.login(user.username, user.password);
                });
            });

            it('should allow visit Pharmacy worklist to users with rights', () => {
                cy.visit('/');
                cy.getBySel('nav-shortcuts-btn').trigger('mouseover');
                cy.getBySel(`menu-panel`).should('be.visible').contains('Pharmacy worklist').click();
                cy.url().should('include', worklist_url);
                cy.get('div.title').should('exist').contains('Pharmacy Worklist');
            });
    });

    context('User with no rights', () => {
            before(() => {
                cy.login();
                cy.createUser(['User']).then(user => {
                    cy.login(user.username, user.password);
                });
            });

            it('Pharmacy worklist link should not be visible', () => {
                cy.visit('/');
                cy.getBySel('nav-shortcuts-btn').trigger('mouseover');
                cy.getBySel(`menu-panel`).should('be.visible')
                    .and('not.contain', 'Pharmacy worklist');

                cy.request({
                    url: worklist_url,
                    failOnStatusCode: false
                }).then((response) => {
                    expect(response.status).to.eq(403);

                });
            });
    });
});
