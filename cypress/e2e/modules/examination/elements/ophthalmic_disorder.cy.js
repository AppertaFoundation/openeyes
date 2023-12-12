describe('ophthalmic disorder widget behaviour', () => {

    // Date added to allow multiple runs in same environment without conflict
    let expectedDisorderTerm1 = 'Expected One ' + Date.now();
    let expectedDisorderTerm2 = 'Expected Two ' + Date.now();
    let expectedDisorderTerm3 = 'Expected Three ' + Date.now();

    beforeEach(() => {
        cy.login()
            .then((context) => {
                return cy.runSeeder('OphCiExamination', 'CommonOphthalmicDisorderWidgetBehaviourSeeder');
            })
            .then((seederData) => {
                return cy.createPatient().then((patient) => {
                    return [patient, seederData];
                })
            })
            .then(([patient, seederData]) => {
                return cy.getEventCreationUrl(patient.id, 'OphCiExamination')
                    .then((url) => {
                        return [url, seederData];
                    });
            })
            .then(([url, seederData]) => {
                return cy.visit(url)
                    .then(() => {
                        cy.addExaminationElement('Ophthalmic Diagnoses');
                    })
                    .then(() => {
                        return seederData;
                    })
            })
            .as('seederData');
    });

    it('only loads common ophthalmic disorders mapped to the current institution, and displays them in the correct order', function () {
        cy.getBySel('add-ophthalmic-diagnoses-button').click();

        cy.getBySel('ophthalmic-diagnoses-popup')
            .should('be.visible')
            .within(() => {
                this.seederData.expected_disorders.forEach((expectedDisorder) => {
                    cy.get(`li[data-id="${expectedDisorder.id}"]`)
                        .should('exist')
                        .scrollIntoView()
                        .should('be.visible');
                });
                this.seederData.unexpected_disorders.forEach((unexpectedDisorder) => {
                    cy.get(`li[data-id="${unexpectedDisorder.id}"]`)
                        .should('not.exist');
                });

                // now check display order
                cy.get('ul[data-id="disorder-list"]')
                    .should('be.visible')
                    .within(() => {
                        let displayedOrderIds = [];

                        cy.get('li')
                            .each((element, index) => {
                                if (this.seederData.sorted_expected_disorder_ids.includes(String(element.data('id')))) {
                                    displayedOrderIds.push(String(element.data('id')));
                                }
                            })
                            .then(() => {
                                expect(displayedOrderIds).deep.to.equal(this.seederData.sorted_expected_disorder_ids);
                            });
                    });
            });

    });

    it('No Ophthalmic Diagnoses checkbox shows on edit', function () {
        let checkboxClass = '.OEModule_OphCiExamination_models_Element_OphCiExamination_Diagnoses_no_ophthalmic_diagnoses_wrapper';

        cy.get(checkboxClass).should('be.visible');

        cy.removeElements('Ophthalmic Diagnoses', true);
        cy.getBySel('add-ophthalmic-diagnoses-button').click();

        cy.getBySel('ophthalmic-diagnoses-popup')
            .should('be.visible')
            .within(() => {
               cy.get('li[data-type="disorder"]').first().click();
                cy.getBySel('add-icon-btn').click();
            });

        cy.get(checkboxClass).should('not.be.visible');

        cy.get('#OphCiExamination_diagnoses')
            .within(() => {
                cy.get('input[data-eye-side="right"]').click();
                cy.get('input[data-eye-side="left"]').click();
                cy.get('#principal_diagnosis_row_key').click();

            });

        cy.saveEvent();

        cy.getBySel('button-event-header-tab-edit').click();
        cy.get(checkboxClass).should('not.be.visible');

        cy.get('.removeDiagnosis').click();
        cy.get(checkboxClass).should('be.visible');

    });
});