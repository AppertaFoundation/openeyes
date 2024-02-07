describe('examination esign behaviour', () => {
    const SETTING_NAME = 'require_pin_for_prescription';

    beforeEach(() => {
        cy.intercept('OphCiExamination/Default/checkPrescriptionAutoSignEnabled').as('checkAutoSignEnabledRequest');
        cy.intercept('OphDrPrescription/PrescriptionCommon/getSetDrugs*').as('prescriptionGetSetDrugs');
        cy.intercept('medicationManagement/getDrugSetForm*').as('mmGetDrugSetForm');
    });

    describe('medication management element is auto signed when pin is not required for signing', () => {
        beforeEach(function () {
            cy.login()
                .then(() => {
                    return cy.createPatient();
                })
                .then((patient) => {
                    return cy.getEventCreationUrl(patient.id, 'OphCiExamination');
                }).then((createUrl) => {


                cy.setSystemSettingValue(SETTING_NAME, 'no')
                    .then(() => {
                        return cy.visit(createUrl);
                    })
                    .then(() => {
                        cy.removeElements('Medication Management', true);
                        cy.addExaminationElement('Medication Management');
                    });
            });
        });

        it(`verifies that signature appears after adding a medication and prescribing it`, () => {
            cy.getElementByName('Medication Management')
                .within(() => {
                    cy.get('#mm-add-medication-btn').click();
                    cy.selectAdderDialogOptionText('Acetazolamide 250mg in 5ml suspension');
                    cy.confirmAdderDialog()
                        .then(() => {
                            cy.get('span.js-btn-prescribe').click();
                        });
                    cy.wait('@checkAutoSignEnabledRequest');
                    cy.get('button#mm-add-medication-btn').scrollIntoView();
                    cy.get('div.js-signature-wrapper').should('be.visible');
                });
        });

        it(`verifies that signature appears after adding a medication set`, () => {
            cy.getElementByName('Medication Management')
                .within(() => {
                    cy.get('#mm-add-standard-set-btn').click();
                    cy.selectAdderDialogOptionText('Post-op');
                    cy.confirmAdderDialog();
                    cy.wait('@prescriptionGetSetDrugs');
                    cy.wait('@mmGetDrugSetForm');
                    cy.wait('@checkAutoSignEnabledRequest');
                    cy.get('button#mm-add-medication-btn').scrollIntoView();
                    cy.get('div.js-signature-wrapper').should('be.visible');
                });
        });

        it('presciber signature is signed after creating prescription from MM when pin is not required', () => {
            cy.addPrescriptionFromMMThenSaveAsDraftAndSignAgain(false);
        })
    });

    describe('medication management element is auto signed when pin is required for signing', () => {
        beforeEach(function () {
            cy.login().as('loggedInUser');

            cy.createPatient().then((patient) => {
                return cy.getEventCreationUrl(patient.id, 'OphCiExamination');
            }).then((createUrl) => {

                cy.setSystemSettingValue(SETTING_NAME, 'yes')
                    .then(() => {
                        return cy.visit(createUrl);
                    })
                    .then(() => {
                        cy.removeElements('Medication Management', true);
                        cy.addExaminationElement('Medication Management');
                    });

            });
        });

            it(`verifies that signature does not appear after adding a medication and prescribing it`, () => {
                cy.getElementByName('Medication Management')
                    .within(() => {
                        cy.get('#mm-add-medication-btn').click();
                        cy.selectAdderDialogOptionText('Acetazolamide 250mg in 5ml suspension');
                        cy.confirmAdderDialog();
                        cy.get('span.js-btn-prescribe').click();
                        cy.wait('@checkAutoSignEnabledRequest');
                        cy.get('button#mm-add-medication-btn').scrollIntoView();
                        cy.get('button.js-sign-button').should('be.visible');
                    });
            });

            it('presciber signature is signed after creating prescription from MM and it deletes it after saving it as draft' +
                'and signature is shown if MM is signed', () => {
                cy.addPrescriptionFromMMThenSaveAsDraftAndSignAgain();
            });

            it(`verifies that signature does not appear after adding a medication set`, () => {
                cy.getElementByName('Medication Management')
                    .within(() => {
                        cy.get('#mm-add-standard-set-btn').click();
                        cy.selectAdderDialogOptionText('Post-op');
                        cy.confirmAdderDialog();
                        // These wait statements ensure enough ajax requests are carried out that
                        // the display will have updated. Could do with being looked at more closely
                        // to make the behaviour more deterministic
                        cy.wait('@prescriptionGetSetDrugs');
                        cy.wait('@mmGetDrugSetForm');
                        cy.wait('@checkAutoSignEnabledRequest');
                        cy.get('button#mm-add-medication-btn').scrollIntoView();
                        cy.get('button.js-sign-button').should('be.visible');
                    });
            });
        });

        after(() => {
            cy.resetSystemSettingValue(SETTING_NAME);
        });
});