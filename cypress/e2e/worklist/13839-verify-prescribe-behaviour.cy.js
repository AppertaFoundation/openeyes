describe('verifies that prescribing behaviour in worklists is constrained by the prescribing rights assigned to the user', () => {
    let worklistPatient;
    before(() => {
        cy.createModels('Worklist', [['withStepsOfType', []]])
            .then((worklist) => {
                return cy.createModels('WorklistPatient', [], {'worklist_id': worklist.id});
            }).as('worklistPatient');
    });

    beforeEach(() => {
        cy.visit('localhost');
    });

    it('ensures that button is clickable when user has prescribe rights', function () {
        // Assigning the worklistPatient object to the worklistPatient variable because the alias worklistPatient
        // is not accessible in the next tests
        worklistPatient = this.worklistPatient;
        // login as admin user who has prescribe rights and navigate to worklist page
        cy.login('admin', 'admin');
        cy.visitWorklist();

        // Close the navbar if it is open
        cy.hideWorklistNavBar();

        cy.getWorklist(this.worklistPatient.worklist_id)
            .within(() => {
                cy.get('table tbody tr').should('have.length', 1);

                // add a prescription step to the patient pathway
                cy.getBySel('add-step').click();
            });

        cy.getBySel('path-step-prescription').click();

        // select the first prescription step for the topmost patient and wait for the popup to become visible
        cy.intercept('*getPathStep*interactive=1*').as('getPathStep')
        cy.getWorklist(this.worklistPatient.worklist_id)
            .within(() => {
                cy.getBySel('prescription').click();
            });
        // cy.getBySel('requested-path-step-prescription').first().click()
        cy.wait('@getPathStep')

        // assert that the Start button is enabled
        cy.getBySel('next-generic-step').should('be.enabled')
    });

    it('ensures that button is not clickable when user does not have prescribe rights', () => {
        // login as nonprescriberuser who does not have prescribe rights and navigate to worklist page
        cy.login('nonprescriberuser', 'password')
        cy.visitWorklist()

        // Close the navbar if it is open
        cy.getBySel('nav-worklist-btn').then(($ele) => {
            if (!$ele.hasClass('open')) {
                $ele.click();
            }
        });

        cy.getBySel('show-patient-pathways').click();

        // Close the navbar if it is open
        cy.hideWorklistNavBar();

        cy.reload();

        // select the first prescription step for the topmost patient and wait for the popup to become visible
        cy.intercept('*getPathStep*interactive=1*').as('getPathStep');

        cy.getWorklist(worklistPatient.worklist_id)
            .within(() => {
                cy.getBySel('prescription').click();
            });

        cy.wait('@getPathStep')

        // assert that the Start button is disabled
        cy.getBySel('next-generic-step').should('be.disabled')
    });
});