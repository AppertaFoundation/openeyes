describe('the behaviour of a patient pathway in a patient summary screen', () => {
    beforeEach(() => {
        cy.login().then(() => {
            cy.runSeeder('', 'PatientSummaryPathwaySeeder').as('seederData');

            cy.createModels('Worklist', [['withStepsOfType', ['checkin', 'discharge']]]).then((worklist) => {
                return cy.createModels('WorklistPatient', [], { 'worklist_id': worklist.id });
            });
        });
    });

    it('refreshes the comment popup correctly after setting a comment', function () {

        cy.visitWorklist();

        cy.getWorklist(this.seederData.worklistId);

        cy.intercept({
            method: 'GET',
            url: '/worklist/getPathStep?partial=1*'
        }).as('getPathStepCheckInPartial');
        cy.intercept({
            method: 'GET',
            url: '/worklist/getPathStep?partial=0*'
        }).as('getPathStepCheckInFull');

        // because two requests are triggered, we separate out the events here to avoid collision
        cy.getBySel(`arr-step-${this.seederData.worklistId}`).trigger('mouseover');
        cy.wait('@getPathStepCheckInPartial');

        cy.getBySel(`arr-step-${this.seederData.worklistId}`).click();

        cy.wait('@getPathStepCheckInFull');

        cy.intercept({
            'method': 'POST',
            'url': '/worklist/checkIn'
        }).as('checkIn');

        cy.getBySel('step-done', ':visible').click();

        cy.wait('@checkIn');

        cy.visit(`/patient/summary/${this.seederData.patientId}`);

        cy.getBySel('clinic-pathway-btn').click();

        cy.intercept({
            method: 'GET',
            url: '/worklist/getPathStep*'
        }).as('getPathStepComment');

        cy.getBySel('pathway-comment-btn', `[data-patient-id="${this.seederData.patientId}"]`).click();

        cy.wait('@getPathStepComment');

        cy.getBySel('pathway-comment-text-input').clear().type('Test comment');

        cy.intercept({
            method: 'GET',
            url: '/worklist/getPathStep*'
        }).as('getPathStepSavedComment');

        cy.getBySel('save-pathway-comment-btn').click();

        cy.wait('@getPathStepSavedComment').its('response.statusCode').should('eq', 200);
    });
});