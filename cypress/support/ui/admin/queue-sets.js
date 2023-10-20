Cypress.Commands.add('addQueueSet', (options = {}) => {
   cy.contains('Admin:');
   cy.contains('Add Queue Set');

   cy.getBySel('add-queueset').click();
   cy.get(`.oe-popup`).should('be.visible').and('contain', 'Add Queue Set');
   cy.get(`.oe-popup`).within(popup => {
      cy.getBySel('queueset-form-name').type(options.name);
      cy.getBySel('queueset-form-description').type('Test Queue Set - description');
      cy.get('input[type="radio"].No').check();

      cy.getBySel(`initial-queue-name`).type('Test Initial Queue Name');
      cy.getBySel(`initial-queue-description`).type('Test Initial Queue Description');
      cy.getBySel(`initial-queue-report_definition`).type('Test Initial Queue Report Definition');

      cy.getBySel(`dialog-ok-button`).click();
   });

   cy.getBySel('patient-ticketing-list').should('contain', options.name);

   if (options.addToInstitution) {
      cy.contains(options.name).closest("tr").find("input[type='checkbox']").check();
      cy.getBySel('admin-map-add').click();

      cy.getBySel('patient-ticketing-list').within(() => {
         cy.contains(options.name)
             .closest("tr")
             .find('i')
             .should('have.class', 'tick');
      });
   }
});

