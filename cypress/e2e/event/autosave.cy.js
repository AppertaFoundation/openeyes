describe('Testing for the auto save functionality', () => {
    let seederData;

    beforeEach(() => {
        cy.login();
    })

    before(() => {
        cy.login()
            .then(() => {
                cy.runSeeder('OphCiExamination', 'AutoSaveSeeder')
                    .then(function (data) {
                        seederData = data;
                    });
                });
    });

    it('Drafts events should be shown in the expected areas of the patient page', () => {
        cy.visit(seederData.patient_url);
        // sidebar
        cy.getBySel('sidebar-draft')
            .should('have.attr', 'data-draft-id', seederData.draft_id);

        // hotlist
        cy.getBySel('hotlist-btn').trigger('mouseover');
        cy.getBySel('hotlist-toggle-drafts').click();
            cy.get(`[data-test="hotlist-draft-event"][data-id="${seederData.draft_id}"]`);

        // new event dialogue
        cy.getBySel('add-new-event-button').click();
        cy.getBySel('add-new-event-draft')
            .should('have.attr', 'data-draft-id', seederData.draft_id);
    });

    it('Update drafts should replay their contents', () => {
        cy.visit(seederData.draft_update_url);
        cy.assertElementValue('systemic-diagnoses-entry-disorder-term', seederData.draft_test_values['systemic-diagnoses-entry-disorder-term']);
    });
});
