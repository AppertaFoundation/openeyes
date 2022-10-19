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
                console.log(["tom", url, patient]);
                cy.visit(url);
                console.log('url done')
                return cy.addExaminationElement('Diagnoses');
            });
    });

    it('only loads diagnoses assigned to the current institution', () =>{

        cy.get('#add-contacts-btn').click();

        cy.get('#contacts-popup');
    });
});