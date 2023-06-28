describe('visual acuity behaviour', () => {
    it('copies a previous visual acuity entry into a new examination event', () => {
        cy.login();

        cy.runSeeder('OphCiExamination', 'VisualAcuityCopyingSeeder', { type: 'visual-acuity' }).then((seederData) => {
            cy.visit(seederData.previousEvent.view_url).then(() => {
                cy.getBySel('visual-acuity-left-eye').find('[data-test="combined-visual-acuity-data"]').contains(seederData.leftEyeCombined);
                cy.getBySel('visual-acuity-right-eye').find('[data-test="combined-visual-acuity-data"]').contains(seederData.rightEyeCombined);
            });

            cy.visitEventCreationUrl(seederData.previousEvent.patient_id, 'OphCiExamination').then(() => {
                cy.removeElements();
                cy.addExaminationElement('Visual Acuity');

                cy.intercept({
                    method: 'GET',
                    url: '/OphCiExamination/default/viewpreviouselements*'
                }).as('viewPreviousElements');

                cy.getBySel('duplicate-element-Visual-Acuity').click().then(() => {
                    cy.wait('@viewPreviousElements').then(() => {
                        cy.intercept({
                            method: 'GET',
                            url: '/OphCiExamination/Default/ElementForm*'
                        }).as('ElementForm');

                        cy.getBySel('copy-previous-element').click().then(() => {
                            cy.wait('@ElementForm').then(() => {
                                // The ids for the existing readings should not be present as their inclusion in the form data
                                // causes an an update on those readings, changing the element_id, instead of creating new ones.
                                cy.getBySel('visual-acuity-reading-id').should('not.exist');

                                cy.getBySel('unable_to_assess-input').should('not.exist');
                                cy.getBySel('eye_missing-input').should('not.exist');

                                cy.getBySel('event-action-save').first().click().then(() => {
                                    // New examination event view should contain copied data
                                    cy.location('pathname').should('contain', '/view');
                                    cy.getBySel('visual-acuity-left-eye').find('[data-test="combined-visual-acuity-data"]').contains(seederData.leftEyeCombined);
                                    cy.getBySel('visual-acuity-right-eye').find('[data-test="combined-visual-acuity-data"]').contains(seederData.rightEyeCombined);
                                });
                            });
                        });
                    });
                });

                cy.visit(seederData.previousEvent.view_url).then(() => {
                    // And the data should still exist for the previous examination event
                    cy.getBySel('visual-acuity-left-eye').find('[data-test="combined-visual-acuity-data"]').contains(seederData.leftEyeCombined);
                    cy.getBySel('visual-acuity-right-eye').find('[data-test="combined-visual-acuity-data"]').contains(seederData.rightEyeCombined);
                });
            });
        });
    });
});
