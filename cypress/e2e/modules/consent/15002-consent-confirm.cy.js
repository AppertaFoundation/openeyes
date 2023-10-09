describe('consent esign behaviour', () => {
    const SETTING_NAME = 'require_pin_for_consent';
    before(() => {
        cy.login()
            .then(() => {
                return cy.runSeeder('OphCoCorrespondence', 'CorrespondencePINSignSeeder');
            }).as('seederData')
            .then(() => {
                cy.setSystemSettingValue(SETTING_NAME, 'no');
            });
    });

    it(`verifies that confirm user details are not the current user details`, () => {
        cy.get('@seederData').then((data)=> {
            cy.login(data.user.username, data.user.password);
            cy.createPatient()
                .then((patient) => {
                    return cy.getEventCreationUrl(patient.id, 'OphTrConsent');
                })
                .then((createUrl) => {
                    cy.consentCompleteAndSave(createUrl);
                })
                .then(() => {
                    cy.get('button#et_confirm').click();
                    cy.location().then((location) => {
                        cy.login();
                        cy.visit(location.pathname);
                        cy.get('#Element_OphTrConsent_Esign_OEModule_OphTrConsent_widgets_EsignUsernamePINField_C td span.js-signatory-name').should('contain', data.user.fullName);
                    });
                });
        });

    });

    after(() => {
        cy.resetSystemSettingValue(SETTING_NAME);
    });
});
