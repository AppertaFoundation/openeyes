describe('pupilliary abnormalities behaviour', () => {
    before(() => {
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
                cy.removeElements();
                return cy.addExaminationElement('Pupils');
            });
    });

    it('allows only one side to be saved successfully', () => {
        cy.removeElementSide('Pupils', 'left');
        cy.get('#OEModule_OphCiExamination_models_PupillaryAbnormalities_right_no_pupillaryabnormalities').click();
        cy.saveEvent()
            .then(() => {
                cy.assertEventSaved();
            });
    });
});