describe('behaviour of the admin screen for clinical pathway presets', function () {
    beforeEach(() => {
        cy.createModels("PathwayType").as("pathway_type1");
        cy.createModels("PathwayType").as("pathway_type2");
        cy.login();
        cy.visit("/Admin/worklist/presetPathways");
    });

    it('toggles the activation status', function() {
        cy.getBySel("checkbox-" + this.pathway_type1.id).click();
        cy.getBySel("toggle-active-btn").click();
        cy.intercept("togglePathwayPresetsActivationStatus");
        cy.getBySel("is-active-" + this.pathway_type1.id).should("not.exist");
        cy.getBySel("is-active-" + this.pathway_type2.id).should("exist");

        cy.getBySel("checkbox-" + this.pathway_type1.id).click();
        cy.getBySel("checkbox-" + this.pathway_type2.id).click();
        cy.getBySel("toggle-active-btn").click();
        cy.intercept("togglePathwayPresetsActivationStatus");
        cy.getBySel("is-active-" + this.pathway_type1.id).should("exist");
        cy.getBySel("is-active-" + this.pathway_type2.id).should("not.exist");
    });
});