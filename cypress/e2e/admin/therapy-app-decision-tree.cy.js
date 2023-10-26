describe('Tests for Therapy Application Decision Tree admin page', () => {
  it('Can add a new Decision Tree with more than 40 chars', () => {
    cy.login();
    cy.visit('OphCoTherapyapplication/admin/viewDecisionTrees');
    cy.getBySel('add-new-btn').click();
    cy.getBySel('decision-tree-name')
      .type("Aflibercept for treating choroidal neovascularisation")
      .should("have.value", "Aflibercept for treating choroidal neovascularisation");
  })
})