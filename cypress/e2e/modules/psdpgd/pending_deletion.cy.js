describe('ensures that entries with associated events pending deletion are not able to be interacted with', () => {
    let seederData;
    
    before(() => {
        cy.login();

        cy.runSeeder('OphDrPGDPSD', 'EventPendingDeletionSeeder')
            .then(function (data) {
                seederData = data;
            });
    });

    beforeEach(() => {
        cy.login(seederData.user.username, seederData.user.password);
    });

    it('verifies event cannot be edited while pending deletion', () => {
        cy.visit(seederData.event.urls.view);

        cy.getBySel('button-event-header-tab-edit').should('not.exist');
        cy.get(`a[data-test="button-event-header-tab-edit"]`)
            .should('not.exist');

        // Trying to go to the edit URL should redirect the user to the patient summary page instead
        cy.visit(seederData.event.urls.edit).then(() => {
            cy.location('pathname').should('eq', `/patient/summary/${seederData.event.patient.id}`);
        });
    });

    it('verifies that entries do not appear in other drug administration events', () => {
        cy.visitEventCreationUrl(seederData.event.patient.id, 'OphDrPGDPSD').then(() => {
            cy.getBySel('drug-administration-section').should('not.exist');
        });
    });

    it('verifies that entries do not appear in examination drug administration element', () => {
        cy.visitEventCreationUrl(seederData.event.patient.id, 'OphCiExamination').then(() => {
            cy.getBySel('drug-administration-section').should('not.exist');
        });
    });

    it('verifies that entries do not appear in the worklist', () => {
        cy.visitWorklist().then(() => {
            cy.get(`[data-test="drug-administration-step"][data-visit-id="${seederData.visitId}"]`).should('not.exist');
        });
    });
});
