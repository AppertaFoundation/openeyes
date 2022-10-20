describe('consent esign behaviour', () => {
    const SETTING_NAME = 'require_pin_for_consent';
    before(() => {
        cy.login()
            .then(() => {
                return cy.createPatient();
            })
            .then((patient) => {
                return cy.getEventCreationUrl(patient.id, 'OphTrConsent');
            }).as('createUrl');
    });

    describe('signature element is auto signed when pin is not required for signing', () => {
        beforeEach(() => {
            cy.login();
            cy.setSystemSettingValue(SETTING_NAME, 'no');
        });

        it.skip(`verifies that signature appears`, function () {
            cy.consentCompleteAndSave(this.createUrl)
                .then(() => {
                    cy.get('div.js-signature-wrapper').first().scrollIntoView().should('be.visible');
                });
        });
    });

    describe('signature element is not auto signed when pin is required for signing', () => {
        beforeEach(() => {
            cy.login();
            cy.setSystemSettingValue(SETTING_NAME, 'yes');
        });

        it.skip(`shows the PIN sign button, indicating that signature is still required.`, function () {
            cy.consentCompleteAndSave(this.createUrl)
                .then(() => {
                    cy.get('button.js-sign-button').first().scrollIntoView().should('be.visible');
                });
        });
    });

    after(() => {
        cy.resetSystemSettingValue(SETTING_NAME);
    });
});
