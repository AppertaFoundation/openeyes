describe('correspondence esign behaviour', () => {
    before(() => {
        cy.resetSystemSettingValue('require_pin_for_correspondence');
    });

    describe('signature element is auto signed when pin is not required for signing', () => {
        beforeEach(() => {
            cy.setSystemSettingValue('require_pin_for_correspondence', 'no');
            cy.login()
                .then(() => {
                    return cy.createPatient();
                })
                .then((patient) => {
                    return cy.getEventCreationUrl(patient.id, 'OphCoCorrespondence')
                        .then((url) => {
                            return [url, patient];
                        });
                })
                .then(([url, patient]) => {
                    cy.visit(url);
                });
        });

        it(`verifies that signature appears`, () => {
            cy.get('div.js-signature-wrapper:first').scrollIntoView().should('be.visible');
        });
    });

    describe('signature management element is auto signed when pin is not required for signing', () => {
        beforeEach(() => {
            cy.setSystemSettingValue('require_pin_for_correspondence', 'yes');
            cy.login()
                .then(() => {
                    return cy.createPatient();
                })
                .then((patient) => {
                    return cy.getEventCreationUrl(patient.id, 'OphCoCorrespondence')
                        .then((url) => {
                            return [url, patient];
                        });
                })
                .then(([url, patient]) => {
                    cy.visit(url);
                });
        });

        it(`verifies that signature does not appear`, () => {
            cy.get('button.js-sign-button:first').scrollIntoView().should('be.visible');
        });
    });

    after(() => {
        cy.resetSystemSettingValue('require_pin_for_correspondence');
    });
});