describe('creation and updating behaviours for the Phasing module', () => {
    beforeEach(() => {
        cy.login();
    });

    it('creates and saves a Phasing event', () => {
        cy.runSeeder('OphCiPhasing', 'SavingSeeder', { 'for': 'create' }).then((seederData) => {
            cy.visitEventCreationUrl(seederData.patientId, 'OphCiPhasing');

            createUpdateTestBody(seederData);
        });
    });

    it('updates and saves a Phasing event', () => {
        cy.runSeeder('OphCiPhasing', 'SavingSeeder', { 'for': 'update' }).then((seederData) => {
            cy.visit(seederData.event.urls.edit);

            createUpdateTestBody(seederData);
        })
    });

    it('glaucoma OESCape does not crash when phasing event is present on patient', () => {
        cy.runSeeder('OphCiPhasing', 'SavingSeeder', { 'for': 'update'}).then((seederData) => {
            cy.visit(`/patient/oescape?subspecialty_id=7&patient_id=${seederData.patientId}`);
            cy.get('#plotly-VA-right').should('exist');
        });
    });


    function createUpdateTestBody(seederData)
    {
        cy.getBySel('phasing-data-column', '[data-side="left"]').within(() => {
            cy.getBySel('reading-value-input').clear().type(seederData.newLeftReading);
        });

        cy.getBySel('phasing-data-column', '[data-side="right"]').within(() => {
            cy.getBySel('reading-value-input').clear().type(seederData.newRightReading);
        });

        cy.saveEvent();

        cy.location('pathname').should('contain', '/view');

        cy.getBySel('timestamped-reading').contains(seederData.newLeftReading);
        cy.getBySel('timestamped-reading').contains(seederData.newRightReading);
    }
});
