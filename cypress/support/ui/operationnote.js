//TODO: this can probably be refactored out into more general methods for filling test data from a fixture or other structured data
Cypress.Commands.add('fillOperationNote', (data) => {
    cy.getBySel('theatre').select(data.elementData.location.theatre);

    cy.get(`[data-test=procedure-side][value=${data.elementData.procedureSide}]`).check();

    for (const procedureRaw of Object.entries(data.elementData.procedures)) {
        let procedure = procedureRaw[1];
        cy.getBySel('add-procedure-btn').click();
        cy.selectAdderDialogOptionText(procedure.procedureName);
        cy.confirmAdderDialog();

        for (const procedureValueRaw of Object.entries(procedure.values)) {
            let procedureValue = procedureValueRaw[1];
            cy.getBySel(procedureValue.testid).then((element) => {
                let inputValue = procedureValue.inputValue;

                //select field and type value
                switch(procedureValue.inputType) {
                    //Pass the dataid for the checkbox and true/false for checked/unchecked
                    case 'checkbox':
                        if (inputValue) {
                            cy.get(element).check();
                        }   else {
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

    cy.get('[data-test=add-pcr-risk-btn]:visible').click();

    for(const pcrValue of data.elementData.pcrRisk) {
        cy.selectAdderDialogOptionAdderID(pcrValue.column, pcrValue.value);
    }

    cy.confirmAdderDialog();

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
                    switch(procedureValue.inputType) {
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