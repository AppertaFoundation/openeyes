describe('Correspondence quick text findings', () => {

    beforeEach(() => {
        cy.login()
            .then(() => {
                return cy.createPatient();
            })
            .as('patient');
    });

    it('insure findings investigation is added', () => {

        cy.get('@patient')
            .then((patient) => {
                cy.visitEventCreationUrl(patient.id, 'OphCiExamination');
                cy.removeElements([], true);
                cy.addExaminationElement('Investigation');

                cy.getBySel('add-investigation-btn').click();

                cy.selectAdderDialogOptionText('A/C tap intravitreal tap');
                cy.confirmAdderDialog();

                cy.saveEvent()
                    .then(() => {
                        cy.assertEventSaved();
                    });

                cy.visitEventCreationUrl(patient.id, 'OphCoCorrespondence');

                cy.get('select[id="findings"]').select('Investigation');

                cy.get('iframe').then(($iframe) => {
                    const $body = $iframe.contents().find('body');
                    cy.wrap($body).get(`table`).should('exist');
                });

            });

    });

});
