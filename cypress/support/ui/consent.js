Cypress.Commands.add('consentCompleteAndSave', (createUrl) => {
    cy.visit(createUrl).then(() => {
        cy.get('input#template1_right_eye').click();
        cy.get('button.booking-select').contains('Consent').click();
        cy.get('input#Element_OphTrConsent_Procedure_AnaestheticType_LA').click();
        cy.contains('Not applicable').click();
        cy.contains('N/A or not offered').click();
        cy.contains('Save draft').first().click();
    });
});