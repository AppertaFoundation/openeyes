describe('pain element behavior', () => {
    beforeEach(() => {
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
                return cy.addExaminationElement('Pain');
            });
    });

    it('saves pain element and is shown on view mode', () => {
        cy.saveEvent().then(() => {
            cy.assertEventSaved();
            cy.getElementByName('Pain').should('have.length', 1);
        });
    });
});
