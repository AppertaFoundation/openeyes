describe('Testing for the auto save functionality', () => {
    let seederData;

    beforeEach(() => {
        //Use a different user when testing drafts as they can change the context of the user
        // and we don't want the context change to affect other tests
        cy.login(seederData.context_change_user.username, seederData.context_change_user.password);
    })
        
    before(() => {
        cy.login()
            .then((body) => {
                cy.runSeeder('OphCiExamination', 'AutoSaveSeeder', {initial_firm_id: body.body.firm_id})
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

    it('Selecting draft event changes user context to match that of the draft event', () => {
        cy.visit(seederData.draft_update_url);

        cy.getBySel('user-profile-firm').should('contain', seederData.draft_context_name);
    });

    it('Update drafts should replay their contents', () => {
        cy.visit(seederData.draft_update_url);
        cy.assertElementValue('systemic-diagnoses-entry-disorder-term', seederData.draft_test_values['systemic-diagnoses-entry-disorder-term']);
    });
});
