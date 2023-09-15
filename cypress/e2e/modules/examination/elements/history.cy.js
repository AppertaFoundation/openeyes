describe('history element behavior', () => {

    beforeEach(() => {
        cy.login()
            .then(() => {
                return cy.createPatient();
            })
            .as('patient');
    });

    it('insure findings investigation is added', () => {

        var testString = "<>!£$%^&*()";

        cy.visit('/OphCiExamination/admin/HistoryMacro/create');

        cy.getBySel('name').type("Test");

        cy.getBySel('body').type(testString);

        cy.getBySel('save-button').click();

        cy.get('@patient')
            .then((data) => {
                cy.visitEventCreationUrl(data.id, 'OphCiExamination');
                cy.removeElements();
                cy.addExaminationElement('History');

                cy.getBySel('add-to-history-template').click();

                cy.get('div[data-test="adder-dialog"]:visible').within(() => {
                    cy.get('li[data-label="Test"]').first().click();
                });

                cy.confirmAdderDialog();

                cy.getBySel('history-description').invoke('val').should('eq', testString);
            });
    });

});