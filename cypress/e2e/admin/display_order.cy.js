describe('Tests for display order functionality in admin pages', () => {
    it('tests Worklist Definitions display order', function () {
        cy.createModels('Worklist', []).as("worklist").then(function () {
                return cy.createModels('WorklistPatient', [], {'worklist_id': this.worklist.id});
            });

        cy.login();
        cy.visit("Admin/worklist/definitions").then(() => {
            cy.get('.sortable>tr:last-child').then(function (row) {
                cy.wrap(row).should("contain", this.worklist.worklist_definition.name);
                cy.dragAndDropRow((row[0].rowIndex - 2), 320, 209);
                cy.intercept("Admin/worklist/sortDefinitions");
            });

            cy.reload();
            cy.getBySel("has-worklists").then(function (row) {
                cy.get('.sortable>tr').eq(row[0].rowIndex - 2).within(() => {
                    cy.getBySel("definition-name").should("contain", this.worklist.worklist_definition.name);
                });
            });
        });
    });
});
