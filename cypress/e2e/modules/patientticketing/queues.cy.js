describe('Queue visibility', () => {
  it('QueueSet Category is not visible in the menu when not assigned to the current institution', () => {
      cy.login()
          .then(() => {
              cy.runSeeder(
                  'PatientTicketing',
                  'QueueSetSeeder',
                  {}
              ).then((data) => {
                  cy.visit("/");
                  cy.getBySel(data.queueset_category_name).should("not.exist");
              });
          });
  });

    it('QueueSet Category is visible in the menu when assigned to the current institution', () => {
        cy.login()
            .then((context) => {
                cy.runSeeder(
                    'PatientTicketing',
                    'QueueSetSeeder',
                    {
                        'with_institution': true
                    }
                ).then((data) => {
                    cy.visit("/");
                    cy.getBySel(data.queueset_category_name).should("exist");
                });
            });
    });

    it('QueueSet is visible in the category only when assigned to the current institution', () => {
        cy.login()
            .then((context) => {
                cy.runSeeder(
                    'PatientTicketing',
                    'QueueSetSeeder',
                    {
                        'one_queue_institution_assigned': true
                    }
                ).then((data) => {
                    cy.visit("/");
                    cy.getBySel("oe-menu").invoke("trigger", "mouseenter").findBySel(data.queueset_category_name)
                    .then($category => {
                        cy.wrap($category).click();
                    });
                    cy.getBySel("change-vc-btn").click();
                    cy.getBySel("queueset-list").children().should("not.contain", data.queuesets_without_institution_names[0]);
                    cy.getBySel("queueset-list").children().should("contain", data.queueset_name);
                });
            });
    });
});
