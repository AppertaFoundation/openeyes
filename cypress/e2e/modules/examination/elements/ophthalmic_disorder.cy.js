describe('ophthalmic disorder widget behaviour', () => {

    // Date added to allow multiple runs in same environment without conflict
    let expectedDisorderTerm1 = 'Expected One ' + Date.now();
    let expectedDisorderTerm2 = 'Expected Two ' + Date.now();
    let expectedDisorderTerm3 = 'Expected Three ' + Date.now();

    before(() => {
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
});