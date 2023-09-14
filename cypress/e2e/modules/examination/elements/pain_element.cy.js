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
    });

    after(() => {
        cy.resetSystemSettingValue("close_incomplete_exam_elements");
    });
});
