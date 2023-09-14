describe('freehand drawing element behaviour', () => {
    it('can restore a draft event with a freehand drawing', () => {
        cy.login().then(() => {
            return cy.createPatient();
        }).then((patient) => {
            cy.visitEventCreationUrl(patient.id, 'OphCiExamination').then(() => {
                cy.removeElements();

                cy.addExaminationElement('Freehand drawing').then((element) => {
                    cy.getBySel('add-freehand-drawing-template-btn').click();

                    cy.get('[data-test="add-options"] li').first().click();
                    cy.getBySel('add-icon-btn').click();

                    cy.intercept('POST', '/OphCiExamination/Default/saveDraft').as('saveDraft');

                    cy.wait('@saveDraft', {requestTimeout: 60000}).then((intercept) => {
                        const draftId = intercept.response.body.draft_id;

                        cy.cancelEvent().then(() => {
                            cy.getBySel('cancel-event-without-discarding-draft-btn').click().then(() => {
                                cy.get(`[data-test="sidebar-draft"][data-draft-id="${draftId}"]`).click().then(() => {
                                    cy.getBySel('Freehand-drawing-element-section');
                                });
                            });
                        });
                    });
                });
            });
        });
    });

    it('sets the is_edited flag when save annotation is clicked', () => {
        cy.login().then(() => {
            return cy.createPatient();
        }).then((patient) => {
            cy.visitEventCreationUrl(patient.id, 'OphCiExamination').then(() => {
                cy.removeElements();

                cy.addExaminationElement('Freehand drawing').then((element) => {
                    cy.getBySel('add-freehand-drawing-template-btn').click();

                    cy.get('[data-test="add-options"] li').first().click();
                    cy.getBySel('add-icon-btn').click();

                    // Set when the template has been added
                    cy.getBySel('freehand-drawing-is-edited-input');

                    cy.getBySel('annotate-freehand-drawing-image-btn').click().then(() => {
                        cy.getBySel('cancel-freehand-drawing-annotation-btn').should('be.visible').click().then(() => {
                            // Removed on cancel
                            cy.getBySel('freehand-drawing-is-edited-input').should('not.exist');

                            cy.getBySel('annotate-freehand-drawing-image-btn').click().then(() => {
                                cy.getBySel('save-freehand-drawing-annotation-btn').click().then(() => {
                                    // Should be set on saving the annotation
                                    cy.getBySel('freehand-drawing-is-edited-input');
                                });
                            });
                        });
                    });
                });
            });
        });
    });

    it('saves the initial template image when the is_edited flag is unset', () => {
        cy.login().then(() => {
            return cy.createPatient();
        }).then((patient) => {
            cy.visitEventCreationUrl(patient.id, 'OphCiExamination').then(() => {
                cy.removeElements();

                cy.addExaminationElement('Freehand drawing').then((element) => {
                    cy.getBySel('add-freehand-drawing-template-btn').click();

                    cy.get('[data-test="add-options"] li').first().click();
                    cy.getBySel('add-icon-btn').click();

                    // Set when the template has been added
                    cy.getBySel('freehand-drawing-is-edited-input');

                    cy.getBySel('annotate-freehand-drawing-image-btn').click().then(() => {
                        cy.getBySel('cancel-freehand-drawing-annotation-btn').should('be.visible').click().then(() => {
                            // Removed on cancel
                            cy.getBySel('freehand-drawing-is-edited-input').should('not.exist');

                            cy.saveEvent().then(() => {
                                cy.assertEventSaved(true);
                            });
                        });
                    });
                });
            });
        });
    });

    it('preserves the template image for an existing event when a draft is saved during an update', () => {
        cy.login().then(() => {
            return cy.createPatient();
        }).then((patient) => {
            cy.visitEventCreationUrl(patient.id, 'OphCiExamination').then(() => {
                cy.removeElements();

                cy.addExaminationElement('Freehand drawing').then((element) => {
                    cy.getBySel('add-freehand-drawing-template-btn').click();

                    cy.intercept({
                        method: 'GET',
                        url: '/ProtectedFile/Download/*'
                    }).as('annotationImage');

                    cy.get('[data-test="add-options"] li').first().click();
                    cy.getBySel('add-icon-btn').click();

                    cy.wait('@annotationImage');

                    cy.getBySel('annotate-freehand-drawing-image-btn').click().then(() => {
                        cy.getBySel('save-freehand-drawing-annotation-btn').should('be.visible').click()
                    });

                    cy.saveEvent().then(() => {
                        cy.assertEventSaved(true);

                        cy.location('href').then((eventViewLocation) => {
                            cy.intercept({
                                method: 'POST',
                                url: 'OphCiExamination/Default/saveDraft'
                            }).as('editingDraftSave');

                            cy.getBySel('button-event-header-tab-edit').click();

                            cy.getBySel('annotate-freehand-drawing-image-btn').click().then(() => {
                                cy.getBySel('save-freehand-drawing-annotation-btn').should('be.visible').click()
                            });

                            cy.wait('@editingDraftSave', {requestTimeout: 60000});

                            cy.visit(eventViewLocation);

                            cy.getBySel('button-event-header-tab-edit').click();

                            cy.visit(eventViewLocation);
                        });
                    });
                });
            });
        });
    });

    it('handles missing images files without throwing exceptions', () => {
        cy.login();

        cy.runSeeder('OphCiExamination', 'ClobberedFreehandDrawingSeeder').then((seederData) => {
            cy.visit(seederData.event.urls.view);

            cy.getBySel('freedraw-entry-file-name').contains('File Missing');

            cy.visit(seederData.event.urls.edit);

            cy.removeElements('Freehand drawing');

            cy.saveEvent().then(() => {
                cy.getBySel('freedraw-entry-file-name', ':visible').contains('File Missing');
            });
        });
    });
});
