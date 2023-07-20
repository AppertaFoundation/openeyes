describe('visual acuity behaviour', () => {
    context('copying previous element data', () => {
        beforeEach(() => {
            cy.login();

            cy.runSeeder('OphCiExamination', 'VisualAcuityCopyingSeeder', { type: 'visual-acuity' })
                .as('seederData');
        });

        it('copies a previous visual acuity entry into a new examination event', () => {
            cy.get('@seederData').then((seederData) => {
                cy.visit(seederData.previousEvent.urls.view).then(() => {
                    cy.getBySel('visual-acuity-left-eye').find('[data-test="combined-visual-acuity-data"]').contains(seederData.leftEyeCombined);
                    cy.getBySel('visual-acuity-right-eye').find('[data-test="combined-visual-acuity-data"]').contains(seederData.rightEyeCombined);
                });

                cy.visitEventCreationUrl(seederData.previousEvent.patient.id, 'OphCiExamination').then(() => {
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
                                    cy.getBySel('visual-acuity-unit-selector').should('have.value', seederData.chosenUnitId);

                                    // The ids for the existing readings should not be present as their inclusion in the form data
                                    // causes an an update on those readings, changing the element_id, instead of creating new ones.
                                    cy.getBySel('visual-acuity-reading-id').should('not.exist');

                                    cy.getBySel('unable_to_assess-input').should('not.be.visible');
                                    cy.getBySel('unable_to_assess-input').should('not.be.checked');
                                    cy.getBySel('eye_missing-input').should('not.be.visible');
                                    cy.getBySel('eye_missing-input').should('not.be.checked');

                                    cy.saveEvent().then(() => {
                                        // New examination event view should contain copied data
                                        cy.location('pathname').should('contain', '/view');
                                        cy.getBySel('visual-acuity-left-eye').find('[data-test="combined-visual-acuity-data"]').contains(seederData.leftEyeCombined);
                                        cy.getBySel('visual-acuity-right-eye').find('[data-test="combined-visual-acuity-data"]').contains(seederData.rightEyeCombined);
                                    });
                                });
                            });
                        });
                    });

                    cy.visit(seederData.previousEvent.urls.view).then(() => {
                        // And the data should still exist for the previous examination event
                        cy.getBySel('visual-acuity-left-eye').find('[data-test="combined-visual-acuity-data"]').contains(seederData.leftEyeCombined);
                        cy.getBySel('visual-acuity-right-eye').find('[data-test="combined-visual-acuity-data"]').contains(seederData.rightEyeCombined);
                    });
                });
            });
        });

        it('does not produce duplicates or eliminate entries when changing the VA Scale after copying previous entries', () => {
            cy.get('@seederData').then((seederData) => {
                cy.visitEventCreationUrl(seederData.previousEvent.patient.id, 'OphCiExamination').then(() => {
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
                                    cy.get('[data-test="visual-acuity-eye-column"][data-side="left"]').find('[data-test="visual-acuity-reading"]').should('have.length', 1);
                                    cy.get('[data-test="visual-acuity-eye-column"][data-side="right"]').find('[data-test="visual-acuity-reading"]').should('have.length', 1);

                                    cy.getBySel('visual-acuity-unit-selector').select(seederData.alternativeUnitName);

                                    // TODO More robust check
                                    // Here the wait accounts for the possible asynchronous addition of duplicate readings
                                    cy.wait(500);

                                    cy.get('[data-test="visual-acuity-eye-column"][data-side="left"]').find('[data-test="visual-acuity-reading"]').should('have.length', 1);
                                    cy.get('[data-test="visual-acuity-eye-column"][data-side="right"]').find('[data-test="visual-acuity-reading"]').should('have.length', 1);
                                });
                            });
                        });
                    });
                });
            });
        });
    });

    context('copying previous complex element data', () => {
        function checkComplexViewData(seederData)
        {
            cy.getBySel('visual-acuity-left-eye').find('[data-test="visual-acuity-reading-method"]').contains(seederData.lhsDetails.method);
            cy.getBySel('visual-acuity-left-eye').find('[data-test="visual-acuity-reading-unit"]').contains(seederData.lhsDetails.unit);
            cy.getBySel('visual-acuity-left-eye').find('[data-test="visual-acuity-reading-value"]').contains(seederData.lhsDetails.value);

            cy.getBySel('visual-acuity-right-eye').find('[data-test="visual-acuity-reading-method"]').contains(seederData.rhsDetails.method);
            cy.getBySel('visual-acuity-right-eye').find('[data-test="visual-acuity-reading-unit"]').contains(seederData.rhsDetails.unit);
            cy.getBySel('visual-acuity-right-eye').find('[data-test="visual-acuity-reading-value"]').contains(seederData.rhsDetails.value);

            cy.getBySel('visual-acuity-beo').find('[data-test="visual-acuity-reading-method"]').contains(seederData.beoDetails.method);
            cy.getBySel('visual-acuity-beo').find('[data-test="visual-acuity-reading-unit"]').contains(seederData.beoDetails.unit);
            cy.getBySel('visual-acuity-beo').find('[data-test="visual-acuity-reading-value"]').contains(seederData.beoDetails.value);
        }

        it('copies a previous complex visual acuity entry into a new examination event', () => {
            cy.login();

            cy.runSeeder('OphCiExamination', 'VisualAcuityCopyingSeeder', { type: 'visual-acuity', complex: true }).then((seederData) => {
                cy.visit(seederData.previousEvent.urls.view).then(() => {
                    checkComplexViewData(seederData);
                });

                cy.visitEventCreationUrl(seederData.previousEvent.patient.id, 'OphCiExamination').then(() => {
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

                                    cy.getBySel('unable_to_assess-input').should('not.be.visible');
                                    cy.getBySel('unable_to_assess-input').should('not.be.checked');
                                    cy.getBySel('eye_missing-input').should('not.be.visible');
                                    cy.getBySel('eye_missing-input').should('not.be.checked');
                                    cy.getBySel('behaviour_assessed-input').should('not.be.visible');
                                    cy.getBySel('behaviour_assessed-input').should('not.be.checked');

                                    cy.getBySel('visual-acuity-add-beo').should('not.be.visible');

                                    cy.saveEvent().then(() => {
                                        // New examination event view should contain copied data
                                        cy.location('pathname').should('contain', '/view');
                                        checkComplexViewData(seederData);
                                    });
                                });
                            });
                        });
                    });

                    cy.visit(seederData.previousEvent.urls.view).then(() => {
                        // And the data should still exist for the previous examination event
                        checkComplexViewData(seederData);
                    });
                });
            });
        });
    });
});
