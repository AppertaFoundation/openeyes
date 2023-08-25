describe('the behaviour of a patient pathway in a patient summary screen', () => {
    before(() => {
        cy.login();

        cy.runSeeder('', 'PatientSummaryPathwaySeeder').as('seederData');

        cy.createModels('Worklist', [['withStepsOfType', ['checkin', 'discharge']]]).then((worklist) => {
            return cy.createModels('WorklistPatient', [], { 'worklist_id': worklist.id });
        });
    });

    it('refreshes the comment popup correctly after setting a comment', function () {
        const seederData = this.seederData;

        cy.visitWorklist();

        cy.getWorklist(seederData.worklistId);

        cy.intercept({
            method: 'GET',
            url: '/worklist/getPathStep*'
        }).as('getPathStepCheckIn')

        cy.getBySel(`arr-step-${seederData.worklistId}`).click();

        cy.wait('@getPathStepCheckIn');

        cy.intercept({
            'method': 'POST',
            'url': '/worklist/checkIn'
        }).as('checkIn');

        cy.getBySel('step-done', ':visible').click();

        cy.wait('@checkIn');

        cy.visit(`/patient/summary/${seederData.patientId}`);

        cy.getBySel('clinic-pathway-btn').click();

        cy.intercept({
            method: 'GET',
            url: '/worklist/getPathStep*'
        }).as('getPathStepComment')

        cy.getBySel('pathway-comment-btn', `[data-patient-id="${seederData.patientId}"]`).click();

        cy.wait('@getPathStepComment');

        cy.getBySel('pathway-comment-text-input').clear().type('Test comment');

        cy.intercept({
            method: 'GET',
            url: '/worklist/getPathStep*'
        }).as('getPathStepSavedComment')

        cy.getBySel('save-pathway-comment-btn').click();

        cy.wait('@getPathStepSavedComment').its('response.statusCode').should('eq', 200);
    });
});
