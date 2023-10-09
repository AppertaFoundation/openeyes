describe('able to save events without popup when virtual review is open ', () => {
    beforeEach(() => {
        cy.login();
    });

    it('create virtual review ticket for patient and try to create events when virtual review is open', () => {
        cy.createPatient()
            .then((patient) => {
                return cy.getEventCreationUrl(patient.id, 'OphCiExamination', 311)
                    .then((url) => {
                        return [url, patient];
                    });
            })
            .then(([url, patient]) => {
                cy.visit(url);

                cy.removeElements();
                cy.addExaminationElement('Follow-up');
                cy.getBySel('show-follow-up-adder').click();
                cy.get('#add-to-follow-up').find('#followup-outcome-options').contains('Virtual Review').scrollIntoView().click()
                cy.get('#add-followup-btn').click();

                cy.get('#patientticketing__priority').select('HIGH');
                cy.get('#patientticketing_eye_problems').select('No');
                cy.get('#patientticketing_drop_application').select('No');
                cy.get('#patientticketing_miss_drops').select('Never forget');
                cy.get('#patientticketing_inhalers').select('No');
                cy.get('#patientticketing_health_changes').select('No');
                cy.get('#patientticketing_questions').select('No');

                cy.saveEvent().then(() => {
                    cy.assertEventSaved();
                });

                cy.visit('/PatientTicketing/default/?cat_id=1');
                const virtualClinicRow = cy.getBySel('virtual-clinic-row', `[data-patient-id=${patient.id}]`);

                virtualClinicRow.scrollIntoView();

                virtualClinicRow.find('.actions').find('.button').contains('Review Patient').click();

                cy.getEventCreationUrl(patient.id, 'OphDrPrescription')
                    .then((url) => {
                        cy.visit(url, {
                            onBeforeLoad(win) {
                                cy.stub(win.console, 'log').as('consoleLog');
                            },
                        });

                        cy.getBySel('add-standard-set-button').click();
                        cy.selectAdderDialogOptionText('Post-op');
                        cy.confirmAdderDialog();

                        cy.getBySel('route-option').each(($el) => {
                            cy.wrap($el).select(1);
                        })

                        cy.getBySel('event-action-save-draft').first().click();

                        cy.get('@consoleLog').should('be.not.calledWith', 'Show Changes you made may not be saved message.');
                        cy.assertEventSaved();
                    });

                cy.getEventCreationUrl(patient.id, 'OphCoCorrespondence').then((url) => {
                    cy.visit(url, {
                        onBeforeLoad(win) {
                            cy.stub(win.console, 'log').as('consoleLog');
                        },
                    });

                    cy.getBySel('letter-type').select('Clinic discharge letter');
                    cy.intercept('/docman/ajaxGetMacroTargets*').as('getMacroTargets');
                    cy.getBySel('letter-template').select('Community Optom');
                    cy.wait('@getMacroTargets');
                    cy.getBySel('event-action-save-draft').first().click();
                    cy.get('@consoleLog').should('be.not.calledWith', 'Show Changes you made may not be saved message.');

                    cy.assertEventSaved();
                });
            });

    });
});
