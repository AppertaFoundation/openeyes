describe('ophthalmic diagnoses widget behaviour', () => {
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
                return cy.addExaminationElement('Diagnoses');
            });
    });

    it('only loads diagnoses assigned to the current institution', () =>{

        cy.get('#add-contacts-btn').click();

        cy.get('#contacts-popup')
            .should('be.visible')
            .within(() => {
                return cy.getModelByAttributes('ContactLabel', {name: 'Next of Kin'})
                    .then((contactLabel) => {
                        // Select next of kin
                        cy.get(`li[data-id="${contactLabel.id}"]`).scrollIntoView();
                        cy.get(`li[data-id="${contactLabel.id}"]`).click();

                        // Select add new
                        cy.get('li[data-type="custom"]').should('be.visible');
                        cy.get('li[data-type="custom"]').click();

                        cy.get('.add-icon-btn').click();
                    })

            });
    });
});