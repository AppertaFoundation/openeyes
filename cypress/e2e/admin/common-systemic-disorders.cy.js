describe('behaviour of the admin screen for common systemic disorders', () => {


    beforeEach(() => {
        cy.login()
            .then(() => {
                return cy.runSeeder(null, 'EditCommonSystemicDisordersSeeder');
            }).as('seederData');
    });

    it('autocompletes disorder entries', function () {

        cy.visit(`/oeadmin/CommonSystemicDisorder/list?institution_id=${this.seederData.institution.id}`);

        cy.getBySel('add-common-systemic-disorder-btn').click().then(() => {
            cy.intercept('/disorder/autocomplete*').as('autocomplete');

            cy.getBySel('disorder-term-input').clear().type(this.seederData.disorder1.term);

            cy.wait('@autocomplete').then(() => {
                cy.getBySel('disorder-autocomplete-list').contains(this.seederData.disorder1.term);
            });
        });
    });

    it('saves new entries along with existing entries', function () {

        cy.visit(`/oeadmin/CommonSystemicDisorder/list?institution_id=${this.seederData.institution.id}`);

        cy.getBySel('add-common-systemic-disorder-btn').click().then(() => {
            cy.intercept('/disorder/autocomplete*').as('autocomplete');

            cy.getBySel('disorder-term-input').clear().type(this.seederData.disorder1.term);

            cy.wait('@autocomplete').then(() => {
                cy.getBySel('autocomplete-match').first().click().then(() => {
                    cy.getBySel('save-common-systemic-disorders-btn').click().then(() => {
                        cy.getBySel('add-common-systemic-disorder-btn').click().then(() => {
                            cy.intercept('/disorder/autocomplete*').as('autocomplete');

                            cy.getBySel('disorder-term-input').clear().type(this.seederData.disorder2.term);

                            cy.wait('@autocomplete').then(() => {
                                cy.getBySel('autocomplete-match').first().click().then(() => {
                                    cy.getBySel('save-common-systemic-disorders-btn').click().then(() => {
                                        cy.getBySel('disorder-term').should('have.length', 2);
                                        cy.getBySel('disorder-term').contains(this.seederData.disorder1.term);
                                        cy.getBySel('disorder-term').contains(this.seederData.disorder2.term);
                                    });
                                });
                            });
                        });
                    });
                });
            });
        });   
    });
});
