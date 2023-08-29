describe('DR Retinopathy element behaviour', () => {
    it('should have the same behaviour for the left and right side entry adders', () => {
        cy.login();

        cy.createPatient().then((patient) => {
            cy.visitEventCreationUrl(patient.id, 'OphCiExamination').then(() => {
                cy.removeElements();
                cy.addExaminationElement('DR Retinopathy');

                for (const side of ['left', 'right']) {
                    cy.getBySel(`add-to-dr-retinopathy-${side}-btn`).click();

                    // Check the headings
                    cy.getBySel('add-header', `[data-id="add-to-retinopathy-r1-${side}"]`).should('not.be.visible');
                    cy.getBySel('add-header', `[data-id="add-to-retinopathy-ma-${side}"]`).should('not.be.visible');
                    cy.getBySel('add-header', `[data-id="add-to-retinopathy-r2-${side}"]`).should('not.be.visible');
                    cy.getBySel('add-header', `[data-id="add-to-retinopathy-r3s-${side}"]`).should('not.be.visible');
                    cy.getBySel('add-header', `[data-id="add-to-retinopathy-r3a-${side}"]`).should('not.be.visible');

                    // Check the lists
                    cy.getBySel('add-options', `[data-id="add-to-retinopathy-r1-${side}"]`).should('not.be.visible');
                    cy.getBySel('add-options', `[data-id="add-to-retinopathy-ma-${side}"]`).should('not.be.visible');
                    cy.getBySel('add-options', `[data-id="add-to-retinopathy-r2-${side}"]`).should('not.be.visible');
                    cy.getBySel('add-options', `[data-id="add-to-retinopathy-r3s-${side}"]`).should('not.be.visible');
                    cy.getBySel('add-options', `[data-id="add-to-retinopathy-r3a-${side}"]`).should('not.be.visible');

                    cy.getBySel('add-options', `[data-id="add-to-retinopathy-dr-${side}"]`).find(`li[data-id="dr-option-${side}"]`).click();

                    cy.getBySel('add-header', `[data-id="add-to-retinopathy-r1-${side}"]`).should('be.visible');
                    cy.getBySel('add-header', `[data-id="add-to-retinopathy-ma-${side}"]`).should('not.be.visible');
                    cy.getBySel('add-header', `[data-id="add-to-retinopathy-r2-${side}"]`).should('be.visible');
                    cy.getBySel('add-header', `[data-id="add-to-retinopathy-r3s-${side}"]`).should('be.visible');
                    cy.getBySel('add-header', `[data-id="add-to-retinopathy-r3a-${side}"]`).should('be.visible');

                    cy.getBySel('add-options', `[data-id="add-to-retinopathy-r1-${side}"]`).should('be.visible');
                    cy.getBySel('add-options', `[data-id="add-to-retinopathy-ma-${side}"]`).should('not.be.visible');
                    cy.getBySel('add-options', `[data-id="add-to-retinopathy-r2-${side}"]`).should('be.visible');
                    cy.getBySel('add-options', `[data-id="add-to-retinopathy-r3s-${side}"]`).should('be.visible');
                    cy.getBySel('add-options', `[data-id="add-to-retinopathy-r3a-${side}"]`).should('be.visible');

                    cy.getBySel('add-options', `[data-id="add-to-retinopathy-r1-${side}"]`).find('[data-label="MA"]').click();

                    cy.getBySel('add-options', `[data-id="add-to-retinopathy-ma-${side}"]`).should('be.visible');
                    cy.getBySel('add-options', `[data-id="add-to-retinopathy-ma-${side}"]`).should('be.visible');

                    cy.getBySel('add-options', `[data-id="add-to-retinopathy-dr-${side}"]`).find(`li[data-id!="dr-option-${side}"]`).click();

                    cy.getBySel('add-header', `[data-id="add-to-retinopathy-r1-${side}"]`).should('not.be.visible');
                    cy.getBySel('add-header', `[data-id="add-to-retinopathy-ma-${side}"]`).should('not.be.visible');
                    cy.getBySel('add-header', `[data-id="add-to-retinopathy-r2-${side}"]`).should('not.be.visible');
                    cy.getBySel('add-header', `[data-id="add-to-retinopathy-r3s-${side}"]`).should('not.be.visible');
                    cy.getBySel('add-header', `[data-id="add-to-retinopathy-r3a-${side}"]`).should('not.be.visible');

                    cy.getBySel('add-options', `[data-id="add-to-retinopathy-r1-${side}"]`).should('not.be.visible');
                    cy.getBySel('add-options', `[data-id="add-to-retinopathy-ma-${side}"]`).should('not.be.visible');
                    cy.getBySel('add-options', `[data-id="add-to-retinopathy-r2-${side}"]`).should('not.be.visible');
                    cy.getBySel('add-options', `[data-id="add-to-retinopathy-r3s-${side}"]`).should('not.be.visible');
                    cy.getBySel('add-options', `[data-id="add-to-retinopathy-r3a-${side}"]`).should('not.be.visible');

                    cy.getBySel('add-icon-btn', ':visible').click();

                    cy.getBySel('dr-retinopathy-side', `[data-side="${side}"]`).find('[data-test="dr-rentinopathy-row"]').should('have.length', 1);
                    cy.getBySel('dr-retinopathy-side', `[data-side="${side}"]`).find('[data-test="dr-rentinopathy-row"]').contains('R0');
                }
            });
        });
    });
});
