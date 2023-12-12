describe('verifies the desired behaviour of the Examination -> Correction Given element', () => {
    
    before(() => {
        
        // create an examination with Correction Given element
        cy.login()
            .then(() => {
                return cy.createPatient();
            })
            .then((patient) => {
                return cy.getEventCreationUrl(patient.id, 'OphCiExamination')
                    .then((url) => {
                        return [url, patient];
                    });
            })
            .then(([url, patient]) => {
                cy.visit(url);
                cy.removeElements([], true);
                return cy.addExaminationElement('Correction Given');
            });

    });

    it('ensures that the element can be completed and saved when one eye is removed', () => {

        // delete the left hand eye
        cy.getBySel('correction-given-remove-left-side').click();

        // fill in details of right eye
        cy.getBySel('correction-given-add-right-side').click();
        cy.selectAdderDialogOptionAdderID('order-as', 'Adjusted');
        cy.selectAdderDialogOptionAdderID('refraction', 'Input Refraction');
        cy.confirmAdderDialog();
        cy.getBySel('correction-given-right-refraction').type('Right refraction');

        // assert that the examination saves without left hand Correction Given value
        cy.saveEvent()
            .then(() => {
                cy.assertEventSaved();
            });

        // assert that the left hand Correction Given remains deleted
        cy.getBySel('correction-given-left-not-recorded').should('be.visible');

    });

});