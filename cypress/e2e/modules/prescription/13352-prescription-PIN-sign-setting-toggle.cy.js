describe('prescription esign behaviour', () => {
    const SETTING_NAME = 'require_pin_for_prescription';
    before(() => {
        cy.login()
            .then(() => {
                return cy.createPatient();
            })
            .then((patient) => {
                return cy.getEventCreationUrl(patient.id, 'OphDrPrescription');
            }).as('createUrl');
    });

    describe('signature element is auto signed when pin is not required for signing', () => {
        beforeEach(function () {
            cy.setSystemSettingValue(SETTING_NAME, 'no');
            cy.login()
                .then(() => {
                    cy.visit(this.createUrl);
                });
        });

        it(`verifies that signature appears`, () => {
            cy.get('div.js-signature-wrapper').scrollIntoView().should('be.visible');
            
            cy.get('#add-standard-set-btn').click();
            cy.selectAdderDialogOptionText('Post-op');
            cy.confirmAdderDialog();

            cy.get('td.route_option_cell > select').each(($el) => {
                cy.wrap($el).select(1);
            });

            cy.get('div.js-signature-wrapper').should('be.visible');

            cy.get('button#et_save').click();
            cy.get('a.button.header-tab').contains('Edit').click();
            cy.get('button#reason_2').click();

            cy.get('div.js-signature-wrapper').should('be.visible');
        });
    });

    describe('signature element is auto signed when pin is not required for signing', () => {
        beforeEach(function () {
            cy.setSystemSettingValue(SETTING_NAME, 'yes');
            cy.login()
                .then(() => {
                    cy.visit(this.createUrl);
                });
        });

        it(`verifies that signature does not appear`, () => {
            cy.get('button.js-sign-button').scrollIntoView().should('be.visible');
        });
    });

    after(() => {
        cy.resetSystemSettingValue(SETTING_NAME);
    });
});