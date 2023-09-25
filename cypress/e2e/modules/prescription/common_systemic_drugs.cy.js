describe('the behaviour for prescription event involving common systemic drugs', () => {
    beforeEach(() => {
        cy.login();

        cy.runSeeder('OphDrPrescription', 'PrescribableCommonSystemicDrugsSeeder').as('seederData');
    });

    it('shows only common systemic drugs that are prescribable in the adder', function () {
        cy.visitEventCreationUrl(this.seederData.patientId, 'OphDrPrescription');

        cy.getBySel('add-prescription-button').click();

        cy.getBySel('add-options', '[data-id="common-systemic"]').within(() => {
            cy.get(`[data-id="${this.seederData.unbrandedPrescribableMedication.id}"]`).should('exist');
            cy.get(`[data-id="${this.seederData.brandedPrescribableMedication.id}"]`).should('exist');
            cy.get(`[data-id="${this.seederData.nonprescribableMedication.id}"]`).should('not.exist');
        });
    });

    it('shows only common systemic drugs that are prescribable and unbranded in the adder search results', function () {
        cy.visitEventCreationUrl(this.seederData.patientId, 'OphDrPrescription');

        cy.getBySel('add-prescription-button').click();

        cy.getBySel('adder-dialog', '[id="add-prescription"]').within(() => {
            cy.intercept('medicationManagement/findRefMedications*').as('search');

            cy.getBySel('adder-search-input').clear().type(this.seederData.nonprescribableMedication.term);

            cy.wait('@search').its('response.body').then((body) => {
                expect(body).to.be.an('array');

                const ids = body.map((result) => {
                    expect(result).to.have.property('id');

                    return result.id;
                });

                expect(ids).to.not.include(this.seederData.nonprescribableMedication.id);
            });

            cy.getBySel('adder-search-input').clear().type(this.seederData.unbrandedPrescribableMedication.term);

            cy.wait('@search').its('response.body').then((body) => {
                expect(body).to.be.an('array');

                const ids = body.map((result) => {
                    expect(result).to.have.property('id');

                    return result.id;
                });

                expect(ids).to.include(this.seederData.unbrandedPrescribableMedication.id);
            });

            cy.getBySel('adder-search-input').clear().type(this.seederData.brandedPrescribableMedication.term);

            cy.wait('@search').its('response.body').then((body) => {
                expect(body).to.be.an('array');

                const ids = body.map((result) => {
                    expect(result).to.have.property('id');

                    return result.id;
                });

                expect(ids).to.not.include(this.seederData.brandedPrescribableMedication.id);
            });
        });
    });
});
