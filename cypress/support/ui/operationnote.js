//TODO: this can probably be refactored out into more general methods for filling test data from a fixture or other structured data
Cypress.Commands.add('fillOperationNote', (data) => {
    cy.getBySel('theatre').select(data.elementData.location.theatre);

    cy.get(`[data-test=procedure-side][value=${data.elementData.procedureSide}]`).check();

    for (const procedureRaw of Object.entries(data.elementData.procedures)) {
        let procedure = procedureRaw[1];
        cy.getBySel('add-procedure-btn').click();
        cy.selectAdderDialogOptionText(procedure.procedureName);
        cy.intercept('OphTrOperationnote/Default/loadElementByProcedure*').as(`loadProcedureElements${procedure.procedureName}`)
        cy.confirmAdderDialog();

        cy.waitFor(`@loadProcedureElements${procedure.procedureName}`);
        // TODO: when testing other procedures, will need to conditionally check for this
        cy.getElementIfExists('.eyedraw-row').then((ele) => {
            if (!ele) {
                return;
            }
            ele.within(() => {
                cy.get('[data-cy-ed-ready="true"]').then(() => {
                    // in lieu of a more robust check on the eyedraw field bindings
                    // we wait half a second to ensure EyeDraw is syncing
                    cy.wait(500);
                });
            });
        });

        for (const procedureValueRaw of Object.entries(procedure.values)) {
            let procedureValue = procedureValueRaw[1];
            cy.getBySel(procedureValue.testid).then((element) => {
                let inputValue = procedureValue.inputValue;

                //select field and type value
                switch (procedureValue.inputType) {
                    //Pass the dataid for the checkbox and true/false for checked/unchecked
                    case 'checkbox':
                        if (inputValue) {
                            cy.get(element).check();
                        } else {
                            cy.get(element).uncheck();
                        }
                        break;
                    //Pass the dataid for the radio button group and the value of the button to be selected
                    case 'radioButton':
                        cy.get(element).check(inputValue);
                        break;
                    //Pass the dataid for the text field and a string to be typed
                    case 'textField':
                        cy.get(element).click().clear().type(inputValue);
                        break;
                    //Pass the dataid for the select element and the string to select
                    case 'select':
                        cy.get(element).select(inputValue);
                        break;
                    //Pass the dataid for the select element and the strings to select
                    case 'multiSelect':
                        for (const toSelect of inputValue) {
                            cy.get(element).select(toSelect);
                        }
                        break;
                    default:
                        throw new Error(`input type ${inputType} is not recognised`);
                }
            });
        }
    }

    cy.getElementIfExists('[data-test=add-pcr-risk-btn]').then((ele) => {
        if (!ele) {
            return;
        }
        ele.click();
        for (const pcrValue of data.elementData.pcrRisk) {
            cy.selectAdderDialogOptionAdderID(pcrValue.column, pcrValue.value);
        }

        cy.confirmAdderDialog();
    });

    cy.getBySel('anaesthetic-type').within(() => {
        cy.contains(data.elementData.anaesthetic.anaestheticType).click();
    });
});

Cypress.Commands.add('verifyOperationNoteData', (data) => {
    for (const procedureRaw of Object.entries(data.elementData.procedures)) {
        let procedure = procedureRaw[1];
        for (const procedureValueRaw of Object.entries(procedure.values)) {
            let procedureValue = procedureValueRaw[1];
            if (procedureValue.verifyValue) {
                cy.getBySel(procedureValue.testid).then((element) => {
                    let inputValue = procedureValue.inputValue;

                    //select field and type value
                    switch (procedureValue.inputType) {
                        //Pass the dataid for the checkbox and true/false for checked/unchecked
                        case 'checkbox':
                            cy.get(element).should('have.value', inputValue);
                            break;
                        //Pass the dataid for the radio button group and the value of the button to be verified
                        case 'radioButton':
                            cy.get(element + `[value=${inputValue}]`).should('be.checked');
                            break;
                        //Pass the dataid for the text field and a string to be verified
                        case 'textField':
                            cy.get(element).should('have.value', inputValue);
                            break;
                        //Pass the dataid for the select element and the string to be verified
                        case 'select':
                            cy.get(element).find('option:selected').should('have.text', inputValue);
                            break;
                        //Pass the dataid for the select element and the strings to be verified
                        case 'multiSelect':
                            for (const toSelect of inputValue) {
                                cy.get(element).parent().parent().contains(toSelect);
                            }
                            break;
                        default:
                            throw new Error(`input type ${inputType} is not recognised`);
                    }
                });
            }
        }
    }
});

Cypress.Commands.add('visitUrlAliasAndSetPinValues', (visitUrlAlias,
    correspondenceSettingValue, prescriptionSettingValue) => {
    const REQUIRE_PIN_CORRESPONDENCE_SIGN_SETTING = 'require_pin_for_correspondence';
    const REQUIRE_PIN_PRESCRIPTION_SIGN_SETTING = 'require_pin_for_prescription';

    cy.setSystemSettingValue(REQUIRE_PIN_CORRESPONDENCE_SIGN_SETTING, correspondenceSettingValue).then(() => {
        cy.setSystemSettingValue(REQUIRE_PIN_PRESCRIPTION_SIGN_SETTING, prescriptionSettingValue).then(() => {
            cy.get(`@${visitUrlAlias}`).then((createUrl) => {
                cy.visit(createUrl);
                cy.contains('Create default op note').click();
            });

        });
    });
});

Cypress.Commands.add('pinSignAndDraftButtonsVisibilityCheck', (fixtureName) => {
    cy.fixture(fixtureName)
        .then((fixture) => {
            let generatePrescriptionButton = cy.getBySel('generate-prescription');
            let generateOptomLetterButton = cy.getBySel('generate-optom-letter');
            let generateGpLetterButton;

            cy.getBySel('event-auto-pin-entry').should(fixture.allOptionsChecked);
            cy.getBySel('save-as-draft-correspondence').scrollIntoView().should('be.visible');
            cy.getBySel('save-as-draft-prescription').should('be.visible');


            generatePrescriptionButton.click();

            cy.getBySel('event-auto-pin-entry').should(fixture.optomLetterChecked);
            cy.getBySel('save-as-draft-correspondence').scrollIntoView().should('be.visible');
            cy.getBySel('save-as-draft-prescription').should('be.not.visible');

            generateOptomLetterButton.click();

            cy.getBySel('event-auto-pin-entry').should(fixture.noOptionsChecked);

            //should see no save draft buttons
            cy.getBySel('save-as-draft-correspondence').scrollIntoView().should('be.not.visible');
            cy.getBySel('save-as-draft-prescription').should('be.not.visible');
            cy.saveEvent();

            generatePrescriptionButton = cy.getBySel('generate-prescription');
            generateOptomLetterButton = cy.getBySel('generate-optom-letter');
            generateGpLetterButton = cy.getBySel('generate-standard-gp-letter');

            generateGpLetterButton.scrollIntoView();

            cy.getBySel('event-auto-pin-entry').should(fixture.noOptionsChecked);
            //should see no save draft buttons
            cy.getBySel('save-as-draft-correspondence').scrollIntoView().should('be.not.visible');
            cy.getBySel('save-as-draft-prescription').should('be.not.visible');

            generateGpLetterButton.click();

            cy.getBySel('event-auto-pin-entry').should(fixture.gpLetterCchecked);
            cy.getBySel('save-as-draft-correspondence').scrollIntoView().should('be.visible');
            cy.getBySel('save-as-draft-prescription').should('be.not.visible');

            generateOptomLetterButton.click();

            cy.getBySel('event-auto-pin-entry').should(fixture.gpAndOptomLetterChecked);
            cy.getBySel('save-as-draft-correspondence').scrollIntoView().should('be.visible');
            cy.getBySel('save-as-draft-prescription').should('be.not.visible');
            generatePrescriptionButton.click();
            generateGpLetterButton.click();

            cy.getBySel('event-auto-pin-entry').should(fixture.prescriptionAndOptomLetterChecked);
            cy.getBySel('save-as-draft-correspondence').scrollIntoView().should('be.visible');
            cy.getBySel('save-as-draft-prescription').should('be.visible');

            cy.saveEvent();
            generateOptomLetterButton = cy.getBySel('generate-optom-letter');
            generateGpLetterButton = cy.getBySel('generate-standard-gp-letter');

            generateGpLetterButton.scrollIntoView();

            cy.getBySel('event-auto-pin-entry').should(fixture.prescriptionAndOptomLetterChecked);
            cy.getBySel('save-as-draft-correspondence').scrollIntoView().should('be.visible');
            cy.getBySel('save-as-draft-prescription').should('be.visible');
            //should see both save as draft buttons

            generateOptomLetterButton.click();

            cy.getBySel('event-auto-pin-entry').should(fixture.prescriptionChecked);

            cy.getBySel('save-as-draft-correspondence').scrollIntoView().should('be.not.visible');
            cy.getBySel('save-as-draft-prescription').should('be.visible');

            generateGpLetterButton.click();

            cy.getBySel('event-auto-pin-entry').should(fixture.prescriptionAndGgpLetterChecked);
            cy.getBySel('save-as-draft-correspondence').scrollIntoView().should('be.visible');
            cy.getBySel('save-as-draft-prescription').should('be.visible');
        });
});

Cypress.Commands.add('checkOperationNoteCreatedEventsPinSign', () => {
    cy.getBySel('sidebar-event-list').find('[data-event-type="Prescription"] a').click();


    cy.getBySel('signatory-name').first().scrollIntoView().contains('Admin Admin');
    cy.getBySel('esigned-at').contains('Signed at');
    cy.getBySel('event-auto-pin-entry').should('have.length', 1);

    cy.checkOperationNoteCreatedCorrespondenceAreSigned();
});

Cypress.Commands.add('checkOperationNoteCreatedCorrespondenceAreSigned', (signatoryName = 'Admin Admin') => {
    cy.getBySel('sidebar-event-list').find('[data-event-type="Correspondence"] a')
        .first().click();

    cy.getBySel('signatory-name').scrollIntoView().contains(signatoryName);
    cy.getBySel('esigned-at').contains('Signed at');
    //There is 2 because there is also secondary signature
    cy.getBySel('event-auto-pin-entry').should('have.length', 2);

    cy.getBySel('sidebar-event-list').find('[data-event-type="Correspondence"]')
        .not('.selected').find('a').click();

    cy.getBySel('signatory-name').scrollIntoView().contains(signatoryName);
    cy.getBySel('esigned-at').contains('Signed at');
    //There is 2 because there is also secondary signature
    cy.getBySel('event-auto-pin-entry').should('have.length', 2);
});

Cypress.Commands.add('checkOperationNoteCreatedEventsDraftStatus', () => {
    cy.getBySel('sidebar-event-list').find('[data-event-type="Prescription"] a').click();

    cy.get('#flash-draft').contains('This prescription is a draft and can still be edited');
    cy.getBySel('event-auto-pin-entry').should('have.length', 1);

    //Check if Correspondences are draft
    cy.getBySel('sidebar-event-list').find('[data-event-type="Correspondence"] a').find('span.draft')
        .should('have.length', 2);

    cy.getBySel('sidebar-event-list').find('[data-event-type="Correspondence"] a')
        .first().click();

    cy.getBySel('unsigned-element-warning').contains('This correspondence must be signed before it can be sent.');
    //There is 2 because there is also secondary signature
    cy.getBySel('event-auto-pin-entry').should('have.length', 2);

    cy.getBySel('sidebar-event-list').find('[data-event-type="Correspondence"]')
        .not('.selected').find('a').click();

    cy.getBySel('unsigned-element-warning').contains('This correspondence must be signed before it can be sent.');
    //There is 2 because there is also secondary signature
    cy.getBySel('event-auto-pin-entry').should('have.length', 2);
});
