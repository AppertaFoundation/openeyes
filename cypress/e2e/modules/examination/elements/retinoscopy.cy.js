describe('Tests for retinoscopy element', () => {
  before(() => {
    cy.login()
        .then(() => {
            return cy.createPatient();
        })
        .then((patient) => {
            return cy.getEventCreationUrl(patient.id, 'OphCiExamination')
                .then((url) => {
                    return [url, patient];
                });
        })
        .then(([url, patient]) => {
            cy.visit(url);
            cy.removeElements([], true);
            return cy.addExaminationElement('Retinoscopy');
        });
});

  it('Comments are displayed in view mode', () => {
    cy.get("#retinoscopy-left-comment-button").click();
    cy.get("#retinoscopy-left-comments").within(() => {
        cy.get("textarea").type("Test comment");
    });
    cy.saveEvent();
    cy.getBySel("retinoscopy-left-comment").should("contain", "Test comment");
  });
})