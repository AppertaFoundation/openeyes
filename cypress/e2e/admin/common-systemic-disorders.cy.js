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

            cy.getBySel('disorder-term-input').clear().type(this.seederData.disorder0.term);

            cy.wait('@autocomplete').then(() => {
                cy.getBySel('disorder-autocomplete-list').should('contain', this.seederData.disorder0.term);
            });
        });
    });

    it('saves new entries along with existing entries', function () {

        cy.visit(`/oeadmin/CommonSystemicDisorder/list?institution_id=${this.seederData.institution.id}`);

        cy.getBySel('add-common-systemic-disorder-btn').click().then(() => {
            cy.intercept('/disorder/autocomplete*').as('autocomplete');

            cy.getBySel('disorder-term-input').clear().type(this.seederData.disorder0.term);

            cy.wait('@autocomplete').then(() => {
                cy.getBySel('autocomplete-match').first().click().then(() => {
                    // alias the first selected disorder for assertion at the end of the test step
                    cy.getBySel('disorder-name0').invoke('text').as('disorderName0');
                    cy.getBySel('save-common-systemic-disorders-btn').click().then(() => {
                        cy.getBySel('add-common-systemic-disorder-btn').click().then(() => {
                            cy.intercept('/disorder/autocomplete*').as('autocomplete');

                            cy.getBySel('disorder-term-input').clear().type(this.seederData.disorder1.term);

                            cy.wait('@autocomplete').then(() => {
                                cy.getBySel('autocomplete-match').first().click().then(() => {
                                    // alias the second selected disorder for assertion at the end of the test step
                                    cy.getBySel('disorder-name1').invoke('text').as('disorderName1');
                                    cy.getBySel('save-common-systemic-disorders-btn').click().then(() => {
                                        cy.getBySel('disorder-term').should('have.length', 2);
                                        // assert that the first selected disorder is in the list
                                        cy.get('@disorderName0').then((diagname0) => {
                                            cy.getBySel('disorder-term').should('contain', diagname0);
                                        });
                                        // assert that the second selected disorder is in the list
                                        cy.get('@disorderName1').then((diagname1) => {
                                            cy.getBySel('disorder-term').should('contain', diagname1);
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
});
