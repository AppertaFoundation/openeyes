describe('CVI creation form behaviour', () => {
    beforeEach(() => {
        cy.login();
    });

    /** NB explicity tests selecting female because the default option is male in the form */
    it('supports defaulting the demographics sex field to female', () => {
        cy.createPatient(['female'])
            .then((patient) => {
                return cy.getEventCreationUrl(patient.id, 'OphCoCvi')
                    .then((url) => {
                        return [url, patient];
                    });
            })
            .then(([url, patient]) => {
                cy.visit(url);

                cy.get('[name$="gender_id]"] option:selected').should('have.text', patient.gender)
            });
    });
});