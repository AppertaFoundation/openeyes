describe('pain element tests', () => {
    describe('pain element behaviour', () => {
        beforeEach(() => {
            cy.login()
                .then(() => {
                    return cy.createPatient();
                })
                .then((patient) => {
                    return cy.getEventCreationUrl(patient.id, 'OphCiExamination')
                        .then((url) => {
                            return [url, patient];
                        });
                })
                .then(([url, patient]) => {
                    cy.visit(url);
                    cy.removeElements();
                    return cy.addExaminationElement('Pain');
                });
        });
    
        it('saves pain element and is shown on view mode', () => {
            cy.getBySel("pain-value-5").click();
            cy.getBySel("pain-add-entry").click();
            cy.saveEvent().then(() => {
                cy.assertEventSaved();
                cy.getElementByName('Pain').should('have.length', 1);
            });
        });
    
        it('saves pain element when pain is selected, but not added and is shown on view mode', () => {
            cy.getBySel("pain-value-5").click();
            cy.saveEvent().then(() => {
                cy.assertEventSaved();
                cy.getElementByName('Pain').should('have.length', 1);
            });
        });
    });


    describe('pain element behaviour subject to system settings', () => {
        it('Discard empty elements appears when saving empty pain element and close_incomplete_exam_elements=on', () => {
            cy.setSystemSettingValue("close_incomplete_exam_elements", "on");
            cy.login()
                .then(() => {
                    return cy.createPatient();
                })
                .then((patient) => {
                    return cy.getEventCreationUrl(patient.id, 'OphCiExamination')
                        .then((url) => {
                            return [url, patient];
                        });
                })
                .then(([url, patient]) => {
                    cy.visit(url);
                    cy.removeElements();
                    return cy.addExaminationElement('Pain');
                });
            cy.saveEvent().then(() => {
                cy.get(".oe-popup .title").should("contain", "Discard empty elements?");
            });
        });
    
        it('Validation error occurs when saving empty pain element and close_incomplete_exam_elements=off', () => {
            cy.setSystemSettingValue("close_incomplete_exam_elements", "off");
            cy.login()
                .then(() => {
                    return cy.createPatient();
                })
                .then((patient) => {
                    return cy.getEventCreationUrl(patient.id, 'OphCiExamination')
                        .then((url) => {
                            return [url, patient];
                        });
                })
                .then(([url, patient]) => {
                    cy.visit(url);
                    cy.removeElements();
                    return cy.addExaminationElement('Pain');
                });
            cy.saveEvent()
            .then(() => {
                cy.getBySel("validation-errors").should("contain", "Pain: Entries cannot be blank");
            });
        });

        it('Retains the recorded pain scores on validation error', () => {
            cy.login()
                .then(() => {
                    return cy.createPatient();
                })
                .then((patient) => {
                    return cy.getEventCreationUrl(patient.id, 'OphCiExamination')
                        .then((url) => {
                            return [url, patient];
                        });
                })
                .then(([url, patient]) => {
                    cy.visit(url);
                    cy.removeElements();
                    return cy.addExaminationElement(['Pain', 'History']);
                });

            cy.getBySel("pain-value-5").click();
            cy.getBySel("pain-add-entry")
                .click()
                .then(() => {
                    cy.saveEvent();

                    cy.getBySel('pain-entries-table').should('be.visible');
                    cy.getBySel(`pain-entries-table`, ' span[id$="score-5"]').then(span => {
                        expect(span.text()).to.eq("5");
                    });
                });            
        });
    });

    after(() => {
        cy.resetSystemSettingValue("close_incomplete_exam_elements");
    });
});
