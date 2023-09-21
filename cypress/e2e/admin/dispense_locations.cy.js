describe('Tests for Drugs > Dispense Locations admin page', () => {
    it('A popup is displayed when deleting a dispense location assigned to a condition', () => {
        cy.login()
            .then((context) => {
                return cy.runSeeder("OphDrPrescription", "DispenseConditionAndLocationSeeder");
            }).then((data) => {
                cy.visit('OphDrPrescription/admin/DispenseLocation/index');
                cy.getBySel("dispense-location-row-" + data.dispense_location_name).within(($row) => {
                    cy.getBySel("dispense-location-assigned-tooltip").should("exist");
                    cy.getBySel("dispense-location-checkbox").check();
                })
                
                cy.getBySel("dispense-location-remove-institution").click();
                cy.get(".oe-popup-content").should("contain", "Some of the selected entries are mapped to a Dispense Condition.");
            });
    });

    it("Can delete a dispense location that is not assigned to any condition", () => {
        cy.login()
            .then((context) => {
                return cy.createModels("OphDrPrescription_DispenseLocation", 
                [["withInstitution", context.body.institution_id]], 
                {}
                );
            }).then((data) => {
                cy.visit('OphDrPrescription/admin/DispenseLocation/index');
                cy.getBySel("dispense-location-row-" + data.name).within(($row) => {
                    cy.getBySel("dispense-location-assigned-tooltip").should("not.exist");
                    cy.getBySel("dispense-location-checkbox").check();
                });
                cy.getBySel("dispense-location-remove-institution").click();
            });
    });
});
