function testFilterInput(filterId, searchTerm, expected){
    const resultId = filterId.split('_')[0]+'_list';

    cy.get(`[data-test=${filterId}]`)
        .should('be.visible')
        .type(searchTerm);
    cy.get('.ui-autocomplete.ui-menu:visible').find('li').contains(expected).click();

    cy.get('#'+resultId).find('li').as('resultLine').should('have.length',1);
    cy.get('@resultLine').find('.remove').click();
    cy.get('@resultLine').should('have.length',0);
}

describe('cvi list filter behaviour', () => {
    describe('find ', () => {

        beforeEach(() => {
            cy.login()
                .then((context) => {
                    context.visit_url = '/OphCoCvi/Default/list';
                })
                .then((context) => {
                    cy.visit(context.visit_url);
                });
        });


        it('Created By filter test', function () {
            testFilterInput('createdby_auto_complete', 'Adm','Mr Admin Admin');
        });

        it('Consultant signed By filter test', function () {
            testFilterInput('consultant_auto_complete','Adm','Mr Admin Admin');
        });

        it('Consultant in charge filter test is case insensitive', function () {
            cy.createModels('Firm', [], {name: "Test Firm " + Date.now()});
            testFilterInput('firm_auto_complete', 'firm', 'Test Firm');
        });

    });
});
