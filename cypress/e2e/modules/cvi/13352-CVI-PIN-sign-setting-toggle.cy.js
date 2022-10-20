describe('cvi esign behaviour', () => {
    const SETTING_NAME = 'require_pin_for_cvi';

    before(() => {
        cy.login()
            .then(() => {
                return cy.createPatient();
            })
            .then((patient) => {
                return cy.getEventCreationUrl(patient.id, 'OphCoCvi');
            }).as('createUrl');
    });

    describe('signature element is auto signed when pin is not required for signing', () => {
        beforeEach(() => {
            cy.login();
            cy.setSystemSettingValue(SETTING_NAME, 'no');
        });

        it(`verifies that signature appears`, function () {
            cy.visit(this.createUrl);
            cy.get('div.js-signature-wrapper').scrollIntoView().should('be.visible');
        });
    });

    describe('signature element is auto signed when pin is not required for signing', () => {
        beforeEach(() => {
            cy.login();
            cy.setSystemSettingValue(SETTING_NAME, 'yes');
        });

        it(`verifies that signature does not appear`, function () {
            cy.visit(this.createUrl);
            cy.get('button.js-sign-button').scrollIntoView().should('be.visible');
        });
    });

    after(() => {
        cy.resetSystemSettingValue(SETTING_NAME);
    });
});