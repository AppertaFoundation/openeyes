describe('near visual acuity behaviour', () => {
    context('copying or editing previous element data', () => {
        beforeEach(() => {
            cy.login();

            cy.runSeeder('OphCiExamination', 'VisualAcuityCopyingSeeder', { type: 'near-visual-acuity' })
                .as('seederData');
        });

        it('permits changing the Near VA Scale when editing', function () {
            cy.visit(this.seederData.previousEvent.urls.edit);

            cy.removeElements('Near Visual Acuity', true);

            cy.intercept({
                method: 'GET',
                url: '/OphCiExamination/Default/ElementForm*'
            }).as('ElementForm');

            cy.getBySel('near-visual-acuity-unit-selector').select(this.seederData.alternativeUnitName);

            cy.wait('@ElementForm');

            cy.saveEvent().then(() => {
                cy.location('pathname').should('contain', '/view');
            });
        });

        it('copies a previous near visual acuity entry into a new examination event', function () {
            cy.visit(this.seederData.previousEvent.urls.view);

            cy.getBySel('near-visual-acuity-left-eye').find('[data-test="combined-near-visual-acuity-data"]').contains(this.seederData.leftEyeCombined);
            cy.getBySel('near-visual-acuity-right-eye').find('[data-test="combined-near-visual-acuity-data"]').contains(this.seederData.rightEyeCombined);

            cy.visitEventCreationUrl(this.seederData.previousEvent.patient.id, 'OphCiExamination');

            cy.removeElements('Near Visual Acuity', true);
            cy.addExaminationElement('Near Visual Acuity');

            cy.intercept({
                method: 'GET',
                url: '/OphCiExamination/default/viewpreviouselements*'
            }).as('viewPreviousElements');

            cy.getBySel('duplicate-element-Near-Visual-Acuity').click().then(() => {
                cy.wait('@viewPreviousElements').then(() => {
                    cy.intercept({
                        method: 'GET',
                        url: '/OphCiExamination/Default/ElementForm*'
                    }).as('ElementForm');

                    cy.getBySel('copy-previous-element').click().then(() => {
                        cy.wait('@ElementForm').then(() => {
                            cy.getBySel('near-visual-acuity-unit-selector').should('have.value', this.seederData.chosenUnitId);

                            // The ids for the existing readings should not be present as their inclusion in the form data
                            // causes an an update on those readings, changing the element_id, instead of creating new ones.
                            cy.getBySel('near-visual-acuity-reading-id').should('not.exist');

                            cy.getBySel('unable_to_assess-input').should('not.be.visible');
                            cy.getBySel('unable_to_assess-input').should('not.be.checked');
                            cy.getBySel('eye_missing-input').should('not.be.visible');
                            cy.getBySel('eye_missing-input').should('not.be.checked');

                            cy.saveEvent().then(() => {
                                // New examination event view should contain copied data
                                cy.location('pathname').should('contain', '/view');
                                cy.getBySel('near-visual-acuity-left-eye').find('[data-test="combined-near-visual-acuity-data"]').contains(this.seederData.leftEyeCombined);
                                cy.getBySel('near-visual-acuity-right-eye').find('[data-test="combined-near-visual-acuity-data"]').contains(this.seederData.rightEyeCombined);
                            });
                        });
                    });
                });
            });

            cy.visit(this.seederData.previousEvent.urls.view);

            // And the data should still exist for the previous examination event
            cy.getBySel('near-visual-acuity-left-eye').find('[data-test="combined-near-visual-acuity-data"]').contains(this.seederData.leftEyeCombined);
            cy.getBySel('near-visual-acuity-right-eye').find('[data-test="combined-near-visual-acuity-data"]').contains(this.seederData.rightEyeCombined);
        });

        it('does not produce duplicates or eliminate entries when changing the VA Scale after copying previous entries', function () {
            cy.visitEventCreationUrl(this.seederData.previousEvent.patient.id, 'OphCiExamination');

            cy.removeElements('Near Visual Acuity', true);
            cy.addExaminationElement('Near Visual Acuity');

            cy.intercept({
                method: 'GET',
                url: '/OphCiExamination/default/viewpreviouselements*'
            }).as('viewPreviousElements');

            cy.getBySel('duplicate-element-Near-Visual-Acuity').click().then(() => {
                cy.wait('@viewPreviousElements').then(() => {
                    cy.intercept({
                        method: 'GET',
                        url: '/OphCiExamination/Default/ElementForm*'
                    }).as('ElementForm');

                    cy.getBySel('copy-previous-element').click().then(() => {
                        cy.wait('@ElementForm').then(() => {
                            cy.get('[data-test="near-visual-acuity-eye-column"][data-side="left"]').find('[data-test="near-visual-acuity-reading"]').should('have.length', 1);
                            cy.get('[data-test="near-visual-acuity-eye-column"][data-side="right"]').find('[data-test="near-visual-acuity-reading"]').should('have.length', 1);

                            cy.getBySel('near-visual-acuity-unit-selector').select(this.seederData.alternativeUnitName);

                            // TODO More robust check
                            // Here the wait accounts for the possible asynchronous addition of duplicate readings
                            cy.wait(500);

                            cy.get('[data-test="near-visual-acuity-eye-column"][data-side="left"]').find('[data-test="near-visual-acuity-reading"]').should('have.length', 1);
                            cy.get('[data-test="near-visual-acuity-eye-column"][data-side="right"]').find('[data-test="near-visual-acuity-reading"]').should('have.length', 1);
                        });
                    });
                });
            });
        });
    });

    context('copying previous complex element data', () => {
        function checkComplexViewData(seederData)
        {
            cy.getBySel('near-visual-acuity-left-eye').find('[data-test="near-visual-acuity-reading-method"]').contains(seederData.lhsDetails.method);
            cy.getBySel('near-visual-acuity-left-eye').find('[data-test="near-visual-acuity-reading-unit"]').contains(seederData.lhsDetails.unit);
            cy.getBySel('near-visual-acuity-left-eye').find('[data-test="near-visual-acuity-reading-value"]').contains(seederData.lhsDetails.value);

            cy.getBySel('near-visual-acuity-right-eye').find('[data-test="near-visual-acuity-reading-method"]').contains(seederData.rhsDetails.method);
            cy.getBySel('near-visual-acuity-right-eye').find('[data-test="near-visual-acuity-reading-unit"]').contains(seederData.rhsDetails.unit);
            cy.getBySel('near-visual-acuity-right-eye').find('[data-test="near-visual-acuity-reading-value"]').contains(seederData.rhsDetails.value);

            cy.getBySel('near-visual-acuity-beo').find('[data-test="near-visual-acuity-reading-method"]').contains(seederData.beoDetails.method);
            cy.getBySel('near-visual-acuity-beo').find('[data-test="near-visual-acuity-reading-unit"]').contains(seederData.beoDetails.unit);
            cy.getBySel('near-visual-acuity-beo').find('[data-test="near-visual-acuity-reading-value"]').contains(seederData.beoDetails.value);
        }

        it('copies a previous complex visual acuity entry into a new examination event', () => {
            cy.login();

            cy.runSeeder('OphCiExamination', 'VisualAcuityCopyingSeeder', { type: 'near-visual-acuity', complex: true }).then((seederData) => {
                cy.visit(seederData.previousEvent.urls.view);
                checkComplexViewData(seederData);

                cy.visitEventCreationUrl(seederData.previousEvent.patient.id, 'OphCiExamination');

                cy.removeElements('Near Visual Acuity', true);
                cy.addExaminationElement('Near Visual Acuity');

                cy.intercept({
                    method: 'GET',
                    url: '/OphCiExamination/default/viewpreviouselements*'
                }).as('viewPreviousElements');

                cy.getBySel('duplicate-element-Near-Visual-Acuity').click().then(() => {
                    cy.wait('@viewPreviousElements').then(() => {
                        cy.intercept({
                            method: 'GET',
                            url: '/OphCiExamination/Default/ElementForm*'
                        }).as('ElementForm');

                        cy.getBySel('copy-previous-element').click().then(() => {
                            cy.wait('@ElementForm').then(() => {
                                // The ids for the existing readings should not be present as their inclusion in the form data
                                // causes an an update on those readings, changing the element_id, instead of creating new ones.
                                cy.getBySel('near-visual-acuity-reading-id').should('not.exist');

                                cy.getBySel('unable_to_assess-input').should('not.be.visible');
                                cy.getBySel('unable_to_assess-input').should('not.be.checked');
                                cy.getBySel('eye_missing-input').should('not.be.visible');
                                cy.getBySel('eye_missing-input').should('not.be.checked');
                                cy.getBySel('behaviour_assessed-input').should('not.be.visible');
                                cy.getBySel('behaviour_assessed-input').should('not.be.checked');

                                cy.getBySel('near-visual-acuity-add-beo').should('not.be.visible');

                                cy.saveEvent().then(() => {
                                    // New examination event view should contain copied data
                                    cy.location('pathname').should('contain', '/view');
                                    checkComplexViewData(seederData);
                                });
                            });
                        });
                    });
                });

                cy.visit(seederData.previousEvent.urls.view);

                // And the data should still exist for the previous examination event
                checkComplexViewData(seederData);
            });
        });
    });
});
