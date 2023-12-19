describe('Advanced Search', () => {
    beforeEach(() => {
        cy.login()
            .then(() => {
                cy.visit('/OECaseSearch/caseSearch/index');
            });
    });

    it('Advanced Search family history has diabetes search', function () {

        cy.getBySel('add-to-search-queries').click();

        cy.getBySel('adder-dialog').within(() => {
            cy.get('li[data-type="FamilyHistoryParameter"]').click();
            cy.contains('span.auto-width', 'INCLUDES').click();
            cy.contains('span.auto-width', 'Diabetes').click();
        });

        cy.getBySel("add-icon-btn").click();

        cy.getBySel("search").click();

        cy.get('div[id="age"]').should('be.visible');
    });
});