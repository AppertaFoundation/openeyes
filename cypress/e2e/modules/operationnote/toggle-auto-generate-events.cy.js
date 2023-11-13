describe('verifies the behaviour of the operation note drug sets drop-down list', () => {
    before(function () {
        cy.createModels("Firm", ["cannotOwnEpisode", "withSubspecialty"]).as("firm");
    });

    it('ensures that the drug sets drop-down list is displayed when Generate prescription is checked (and hidden when not)', () => {
        // login as admin
        cy.login()

        // create a patient then create an operation note for said patient
        cy.createPatient()
            .then(function (patient) {
                return cy.getEventCreationUrl(patient.id, 'OphTrOperationnote', this.firm.id)
                    .then((url) => {
                        cy.visit(url)
                    })
                })

        // click the 'Create minor ops note' button
        cy.getBySel('create-minor-ops-note').click()

        // assert that the drug (standard) sets drop-down list is NOT displayed
        cy.getBySel('drug-sets-list').should('not.be.visible')

        // under the Comments section check 'Generate standard GP letter', then check 'Generate prescription'
        cy.getBySel('generate-standard-gp-letter').check()
        cy.getBySel('generate-prescription').check()

        // assert that the drug (standard) sets drop-down list is displayed
        cy.getBySel('drug-sets-list').should('be.visible')

        // uncheck 'Generate prescription' and assert that the drug (standard) sets drop-down list is hidden
        cy.getBySel('generate-prescription').uncheck()
        cy.getBySel('drug-sets-list').should('not.be.visible')
    })
    
})