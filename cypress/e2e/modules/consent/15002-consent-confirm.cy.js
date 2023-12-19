describe('consent esign behaviour', () => {
    const SETTING_NAME = 'require_pin_for_consent';
    beforeEach(() => {
        cy.login()
            .then(() => {
                return cy.runSeeder('OphCoCorrespondence', 'CorrespondencePINSignSeeder');
            }).as('seederData');
    });

    describe('consent esign behaviour when setting is NO', () => {

        beforeEach(() => {
            cy.setSystemSettingValue(SETTING_NAME, 'no');
        })

        it(`verifies that confirm user details are not the current user details `, () => {
            cy.get('@seederData').then((data) => {
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

        it(`verifies that confirm user details are the ones that confirmed it when the confirmed user is not the one who created the event `, () => {
            cy.get('@seederData').then((data) => {
                cy.login(data.user.username, data.user.password);
                cy.createPatient()
                    .then((patient) => {
                        return cy.getEventCreationUrl(patient.id, 'OphTrConsent');
                    })
                    .then((createUrl) => {
                        cy.consentCompleteAndSave(createUrl);
                    })
                    .then(() => {
                        cy.location().then((location) => {
                            cy.login();
                            cy.get('button#et_confirm').click();
                            cy.visit(location.pathname);
                            cy.get('#Element_OphTrConsent_Esign_OEModule_OphTrConsent_widgets_EsignUsernamePINField_C')
                                .find('.js-signatory-name')
                                .invoke('text')
                                .should('include', 'Admin Admin');
                            cy.login(data.user.username, data.user.password);
                            cy.visit(location.pathname);
                            cy.get('#Element_OphTrConsent_Esign_OEModule_OphTrConsent_widgets_EsignUsernamePINField_C')
                                .find('.js-signatory-name')
                                .invoke('text')
                                .should('include', 'Admin Admin');
                        });
                    });
            });
        });
    });

    describe('consent esign behaviour when setting is YES', () => {
        beforeEach(() => {
            cy.setSystemSettingValue(SETTING_NAME, 'yes');
        });

        it(`verifies that confirm user details are not the current user details `, () => {
            cy.get('@seederData').then((data) => {
                cy.login(data.user.username, data.user.password);
                cy.createPatient()
                    .then((patient) => {
                        return cy.getEventCreationUrl(patient.id, 'OphTrConsent');
                    })
                    .then((createUrl) => {
                        cy.consentCompleteAndSave(createUrl);
                    })
                    .then(() => {
                        cy.location().then((location) => {
                            cy.login().then((loggedInUser) => {
                                    cy.visit(location.pathname);
                                    cy.get('button#et_confirm').click();
                                    cy.intercept('/OphTrConsent/default/getSignatureByUsernameAndPin*').as('getSignature');

                                    const confirmedByRow = cy.get('#Element_OphTrConsent_Esign_OEModule_OphTrConsent_widgets_EsignUsernamePINField_C');
                                    confirmedByRow.find('.js-pin-input').type(loggedInUser.body.pincode);

                                    cy.get('#Element_OphTrConsent_Esign_OEModule_OphTrConsent_widgets_EsignUsernamePINField_C')
                                        .find(`.try-pin`)
                                        .should('be.visible').click();
                                    cy.wait('@getSignature');

                                    cy.get('#Element_OphTrConsent_Esign_OEModule_OphTrConsent_widgets_EsignUsernamePINField_C td span.js-signatory-name')
                                        .should('contain', 'Admin Admin');

                                    cy.login(data.user.username, data.user.password);

                                    cy.get('#Element_OphTrConsent_Esign_OEModule_OphTrConsent_widgets_EsignUsernamePINField_C td span.js-signatory-name')
                                        .should('contain', 'Admin Admin');
                                }
                            );
                        });
                    });
            });
        });
    });

    after(() => {
        cy.resetSystemSettingValue(SETTING_NAME);
    });
});
