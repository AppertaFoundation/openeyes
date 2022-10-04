const { iteratee } = require("lodash");

describe('Operation booking procedure selection behaviour', () => {
    before(() => {
        cy.createPatient().as('patient');
    });

    beforeEach(() => {
        cy.login().then((context) => {
            cy.createModels('Procedure', [['forSubspecialtyIds', [context.body.subspecialty_id], context.body.institution_id]], {active: 1})
                .as('activeProcedure');
            cy.createModels('Procedure', [['forSubspecialtyIds', [context.body.subspecialty_id], context.body.institution_id]], {active: 0})
                .as('inactiveProcedure');
        });
    });

    it('only shows active procedures in the adder dialog', function () {
        cy.getEventCreationUrl(this.patient.id, 'OphTrOperationbooking')
            .then((url) => {
                cy.visit(url);
                cy.getBySel('add-procedure-btn').click();
                cy.get('.js-test-adder')
                    .should('be.visible')
                    .within(() => {
                        cy.get('table').should('include.text', this.activeProcedure.term);
                        cy.get('table').should('not.include.text', this.inactiveProcedure.term);
                    });
            });
    });
});