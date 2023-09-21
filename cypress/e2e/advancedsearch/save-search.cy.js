describe('Saving Advanced Search', () => {

    beforeEach(() => {
        cy.login()
            .then(() => {
                cy.visit('/OECaseSearch/caseSearch/index');
            });
    });

    it('save advanced search with previous trial criteria', function () {

        cy.getBySel('add-to-search-queries').click();

        cy.getBySel('adder-dialog').within(() => {
            cy.get('li[data-type="PreviousTrialParameter"]').click();

            cy.contains('span.auto-width', 'INCLUDES').click();

            cy.contains('span.auto-width', 'Intervention').click();

        });

        cy.getBySel("add-icon-btn").click();

        cy.getBySel("save-search").click();

        cy.getBySel('search-name').type('trial criteria test');

        cy.getBySel('save-search-btn').click();

        cy.getBySel('load-saved-search').click();

        cy.getBySel('searches').within(() => {
            cy.contains('td', 'trial criteria test').should('exist');
        });

    });

})