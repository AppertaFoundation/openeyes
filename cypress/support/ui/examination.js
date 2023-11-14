Cypress.Commands.add('addExaminationElement', (elementNames, validateElementsAdded=true) => {
    if (!Array.isArray(elementNames)) {
        elementNames = [elementNames];
    }

    cy.get('#js-manage-elements-btn').click();
    elementNames.forEach((elementName) => {
        const kebabCaseElementName = elementName.replace(/[() /&]/g, '-');
        cy.get(`#manage-elements-${kebabCaseElementName}`).within((button) => {
            if (!button.hasClass('added') && !button.hasClass('mandatory')) {
                button.click();
                // Wait for the element to be added to the page
                cy.intercept({
                    method: 'GET',
                    url: '/OphCiExamination/Default/ElementForm*'
                }).as('ElementForm');
                cy.wait('@ElementForm');
            }
        });
    });

    cy.get('#manage-elements-nav .close-icon-btn button').click();

    if (validateElementsAdded) {
        elementNames.forEach((elementName) => {
            cy.getElementByName(elementName).scrollIntoView().should('be.visible');
        });
    }
});

Cypress.Commands.add('addPrescriptionFromMMThenSaveAsDraftAndSignAgain', (pinSigningRequired = true) => {
    cy.getElementByName('Medication Management')
        .within(() => {
            cy.get('#mm-add-medication-btn').click();
            cy.selectAdderDialogOptionText('Acetazolamide 250mg in 5ml suspension');
            cy.confirmAdderDialog();
            cy.get('span.js-btn-prescribe').click();
            cy.wait('@checkAutoSignEnabledRequest');
            cy.get('button#mm-add-medication-btn').scrollIntoView();

            cy.getBySel('event-medication-management-row').within(() => {
                cy.get('.js-dose').type('1');
                cy.get('.js-frequency').select(1);
                cy.get('.js-duration').select(1);
                cy.intercept('/OphDrPrescription/PrescriptionCommon/GetDispenseLocation*').as('getDispenseLocation');
                cy.get('.js-dispense-condition').select(1);
                cy.wait('@getDispenseLocation');
            });

            if (pinSigningRequired) {
                cy.intercept('/OphCiExamination/default/getSignatureByPin*').as('getSignature');
                cy.get('@loggedInUser').then((loggedInUser) => {
                    cy.get('input.js-pin-input ').type(loggedInUser.body.pincode);
                });
                cy.get('button.js-sign-button').click();

                cy.wait('@getSignature');
            }
        });

    cy.saveEvent();
    cy.assertEventSaved();

    cy.getBySel('sidebar-event-list').find('[data-event-type="Prescription"] a').click();

    cy.get('#Element_OphDrPrescription_Esign_EsignPINField_0').within(() => {
        cy.get('.js-signatory-label').contains('Prescriber');
        cy.get('.esigned-at').should('be.visible').contains('Signed at');
    })

    //If pin is not required we cannot get draft prescription
    if (pinSigningRequired) {

        cy.getBySel('sidebar-event-list').find('[data-event-type="Examination"] a').click();

        cy.getBySel('button-event-header-tab-edit').click();

        cy.getBySel('event-medication-management-row').within(() => {
            cy.get('.js-frequency').select(2);
        })

        cy.getBySel('save-as-draft-prescription').click();

        cy.saveEvent();

        cy.getBySel('prescription-edit-reasons').select(1);
        cy.getBySel('submit-prescription-reason').click();

        cy.getBySel('sidebar-event-list').find('[data-event-type="Prescription"] a').click();

        cy.get('#Element_OphDrPrescription_Esign_EsignPINField_0').within(() => {
            cy.get('.js-signatory-label').contains('Prescriber');
            cy.getBySel('pin-sign-button').scrollIntoView().should('be.visible');
        })

        cy.getBySel('sidebar-event-list').find('[data-event-type="Examination"] a').click();
        cy.getBySel('button-event-header-tab-edit').click();

        cy.intercept('/OphCiExamination/default/getSignatureByPin*').as('getSignature');
        cy.get('@loggedInUser').then((loggedInUser) => {
            cy.get('input.js-pin-input ').type(loggedInUser.body.pincode);
        });
        cy.get('button.js-sign-button').click();

        cy.wait('@getSignature');

        cy.saveEvent();

        cy.getBySel('sidebar-event-list').find('[data-event-type="Prescription"] a').click();

        cy.get('#Element_OphDrPrescription_Esign_EsignPINField_0').within(() => {
            cy.get('.js-signatory-label').contains('Prescriber');
            cy.get('.esigned-at').should('be.visible').contains('Signed at');
        });
    }
});