describe('the summary page is rendered correctly for a patient', () => {
    it('shows allergy details in an alert popup box', () => {
        cy.login();
        cy.createEvent('OphCiExamination', [
            ['withAllergies', 'withEntries']
        ]).then((eventData) => {
            cy.visit(eventData.urls.view);
            cy.getBySel('summary-allergies-alert-btn').click();
            cy.getBySel('summary-allergies-popup')
                .should('be.visible')
                .contains(eventData.elements[0].attributes.entries[0].allergy.name);
        })
    });
});