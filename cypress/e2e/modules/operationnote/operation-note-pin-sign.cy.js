describe('op note pin sign functionality', () => {
    const REQUIRE_PIN_CORRESPONDENCE_SIGN_SETTING = 'require_pin_for_correspondence';
    const REQUIRE_PIN_PRESCRIPTION_SIGN_SETTING = 'require_pin_for_prescription';

    describe('op note sign functionality as admin', () => {
        beforeEach(() => {
            cy.login();

            cy.createPatient(['withGp', 'withPractice'])
                .its('id')
                .then((patientId) => {
                    return cy.getEventCreationUrl(patientId, 'OphTrOperationnote', 301);
                }).as('createUrl');
        });

        it('creates an operation note with pin required and filled and creates signed correspondence and prescription events', () => {
            cy.visitUrlAliasAndSetPinValues('createUrl', 'yes', 'yes');

            cy.fixture('13040-operation-note-templates')
                .then((fixture) => {
                    cy.intercept('/OphTrOperationnote/default/getSignatureByUsernameAndPin*').as('getSignature');
                    cy.getBySel('event-auto-pin-entry').type('111111');
                    cy.getBySel('event-auto-sign-by-pin-button').click();
                    cy.wait('@getSignature');

                    cy.getBySel('esigned-at').contains('Signed at');

                    cy.saveEvent().then(() => {
                        cy.fillOperationNote(fixture.templateData).then(() => {

                            cy.getBySel('esigned-at').contains('Signed at');

                            cy.saveEvent().then(() => {
                                cy.checkOperationNoteCreatedEventsPinSign();
                            });
                        });
                    });
                });
        });

        it('creates an operation note with pin required and filled and with save draft buttons checked' +
            'and creates draft correspondence and prescription events', () => {
            cy.visitUrlAliasAndSetPinValues('createUrl', 'yes', 'yes');

            cy.fixture('13040-operation-note-templates')
                .then((fixture) => {


                    cy.saveEvent().then(() => {
                        cy.fillOperationNote(fixture.templateData).then(() => {

                            cy.intercept('/OphTrOperationnote/default/getSignatureByUsernameAndPin*').as('getSignature');
                            cy.getBySel('event-auto-pin-entry').type('111111');
                            cy.getBySel('event-auto-sign-by-pin-button').click();
                            cy.wait('@getSignature');

                            cy.getBySel('esigned-at').contains('Signed at');

                            cy.getBySel('save-as-draft-correspondence').click();
                            cy.getBySel('save-as-draft-prescription').click();

                            cy.saveEvent().then(() => {
                                cy.getBySel('sidebar-event-list').find('[data-event-type="Prescription"] a').click();

                                cy.get('#flash-draft').contains('This prescription is a draft and can still be edited');
                                cy.getBySel('event-auto-pin-entry').should('have.length', 1);

                                cy.checkOperationNoteCreatedEventsDraftStatus();
                            });
                        });
                    });
                });
        });

        it('creates an operation note with pin required and filled and with save draft prescription checked' +
            'and creates draft correspondence and prescription events', () => {
            cy.visitUrlAliasAndSetPinValues('createUrl', 'yes', 'yes');

            cy.fixture('13040-operation-note-templates')
                .then((fixture) => {


                    cy.saveEvent().then(() => {
                        cy.fillOperationNote(fixture.templateData).then(() => {

                            cy.intercept('/OphTrOperationnote/default/getSignatureByUsernameAndPin*').as('getSignature');
                            cy.getBySel('event-auto-pin-entry').type('111111');
                            cy.getBySel('event-auto-sign-by-pin-button').click();
                            cy.wait('@getSignature');

                            cy.getBySel('esigned-at').contains('Signed at');

                            cy.getBySel('save-as-draft-prescription').click();

                            cy.saveEvent().then(() => {
                                cy.checkOperationNoteCreatedCorrespondenceAreSigned();
                            });
                        });
                    });
                });
        });


        it('creates an operation note with no pin required and filled and with save draft buttons checked' +
            'and creates draft correspondence and prescription events', () => {
            cy.visitUrlAliasAndSetPinValues('createUrl', 'no', 'no');

            cy.fixture('13040-operation-note-templates')
                .then((fixture) => {
                    cy.saveEvent().then(() => {
                        cy.fillOperationNote(fixture.templateData).then(() => {

                            cy.getBySel('save-as-draft-correspondence').click();
                            cy.getBySel('save-as-draft-prescription').click();

                            cy.saveEvent().then(() => {
                                cy.checkOperationNoteCreatedEventsDraftStatus();
                            });
                        });
                    });
                });
        });

        it('creates an operation note without pin required and creates signed correspondence and prescription events', () => {
            cy.visitUrlAliasAndSetPinValues('createUrl', 'no', 'no');
            cy.fixture('13040-operation-note-templates')
                .then((fixture) => {
                    cy.fillOperationNote(fixture.templateData).then(() => {
                        cy.saveEvent().then(() => {
                            cy.checkOperationNoteCreatedEventsPinSign();
                        });
                    });
                });
        });

        it('shows and hides pin box and draft buttons correctly when both settings are on', () => {
            cy.visitUrlAliasAndSetPinValues('createUrl', 'yes', 'yes');
            cy.pinSignAndDraftButtonsVisibilityCheck('pin-sign-button-visibility-both-settings-on');
        });

        it('shows and hides pin box and draft buttons correctly when both settings are off', () => {
            cy.visitUrlAliasAndSetPinValues('createUrl', 'no', 'no');
            cy.pinSignAndDraftButtonsVisibilityCheck('pin-sign-button-visibility-both-settings-off');
        });

        it('shows and hides pin box and draft buttons correctly when correspondence off and prescription on', () => {
            cy.visitUrlAliasAndSetPinValues('createUrl', 'no', 'yes');
            cy.pinSignAndDraftButtonsVisibilityCheck('pin-sign-button-visibility-prescription-on-correspondence-off');
        });

        it('shows and hides pin box and draft buttons correctly when correspondence on and prescription off', () => {
            cy.visitUrlAliasAndSetPinValues('createUrl', 'yes', 'no');
            cy.pinSignAndDraftButtonsVisibilityCheck('pin-sign-button-visibility-prescription-off-correspondence-on');
        });

        it('shows and hides pin box and draft buttons correctly when correspondence on and prescription off', () => {
            cy.visitUrlAliasAndSetPinValues('createUrl', 'yes', 'yes');

            cy.getBySel('save-as-draft-correspondence').click();
            cy.getBySel('event-auto-pin-entry').should('be.visible');
            cy.getBySel('save-as-draft-prescription').click();
            cy.getBySel('event-auto-pin-entry').should('be.not.visible');
            cy.getBySel('save-as-draft-correspondence').click();
            cy.getBySel('event-auto-pin-entry').should('be.visible');
            cy.getBySel('save-as-draft-prescription').click();
            cy.getBySel('event-auto-pin-entry').should('be.visible');

            let generatePrescriptionButton = cy.getBySel('generate-prescription');
            let generateOptomLetterButton = cy.getBySel('generate-optom-letter');
            let generateGpLetterButton = cy.getBySel('generate-standard-gp-letter');

            generateOptomLetterButton.click();
            generateGpLetterButton.click();

            cy.getBySel('event-auto-pin-entry').should('be.visible');
            cy.getBySel('save-as-draft-prescription').click();
            cy.getBySel('event-auto-pin-entry').should('be.not.visible');

            generatePrescriptionButton.click();
            generateOptomLetterButton.click();

            cy.getBySel('event-auto-pin-entry').should('be.visible');
            cy.getBySel('save-as-draft-correspondence').click();
            cy.getBySel('event-auto-pin-entry').should('be.not.visible');
        });
    });

    describe('op note sign functionality as non prescriber user', () => {
        beforeEach(() => {
            cy.login('nonprescriberuser', 'password');
            cy.createPatient(['withGp', 'withPractice'])
                .its('id')
                .then((patientId) => {
                    return cy.getEventCreationUrl(patientId, 'OphTrOperationnote', 301);
                }).as('createUrl');
        });


        it('prescription save draft pre-selected and disabled when going to op note', () => {
            cy.visitUrlAliasAndSetPinValues('createUrl', 'yes', 'yes');
            let saveAsDraftButton = cy.getBySel('save-as-draft-prescription');

            saveAsDraftButton.scrollIntoView().should('be.visible');
            saveAsDraftButton.should('be.checked');
            saveAsDraftButton.should('be.disabled');

            cy.getBySel('no-prescribe-rights-tooltip').should('be.visible');

            cy.getBySel('generate-prescription').click();

            saveAsDraftButton = cy.getBySel('save-as-draft-prescription');
            saveAsDraftButton.should('be.not.visible');
            cy.getBySel('no-prescribe-rights-tooltip').should('be.not.visible');

            cy.saveEvent().then(() => {

                saveAsDraftButton = cy.getBySel('save-as-draft-prescription');

                saveAsDraftButton.scrollIntoView().should('be.not.visible');


                cy.getBySel('no-prescribe-rights-tooltip').should('be.not.visible');

                cy.getBySel('generate-prescription').click();

                saveAsDraftButton = cy.getBySel('save-as-draft-prescription');

                saveAsDraftButton.should('be.visible');
                saveAsDraftButton.should('be.checked');
                saveAsDraftButton.should('be.disabled');

                cy.getBySel('no-prescribe-rights-tooltip').should('be.visible');
            });
        });

        it('enter pin and save correspondence as final prescription as draft', () => {
            cy.visitUrlAliasAndSetPinValues('createUrl', 'yes', 'yes');
            cy.fixture('13040-operation-note-templates')
                .then((fixture) => {
                    cy.intercept('/OphTrOperationnote/default/getSignatureByUsernameAndPin*').as('getSignature');
                    cy.getBySel('event-auto-pin-entry').type('464979');
                    cy.getBySel('event-auto-sign-by-pin-button').click();
                    cy.wait('@getSignature');

                    cy.getBySel('esigned-at').contains('Signed at');

                    cy.saveEvent().then(() => {
                        cy.getBySel('surgeon-value').select(2);
                        cy.fillOperationNote(fixture.templateData).then(() => {

                            cy.getBySel('esigned-at').contains('Signed at');

                            cy.saveEvent().then(() => {
                                cy.getBySel('sidebar-event-list').find('[data-event-type="Prescription"] a').click();

                                cy.get('#flash-draft').contains('This prescription is a draft and can still be edited');
                                cy.getBySel('event-auto-pin-entry').should('have.length', 1);

                                cy.checkOperationNoteCreatedCorrespondenceAreSigned('non prescriber user');
                            });
                        });
                    });
                });
        });
    });

    after(() => {
        cy.resetSystemSettingValue(REQUIRE_PIN_CORRESPONDENCE_SIGN_SETTING);
        cy.resetSystemSettingValue(REQUIRE_PIN_PRESCRIPTION_SIGN_SETTING);
    });
});