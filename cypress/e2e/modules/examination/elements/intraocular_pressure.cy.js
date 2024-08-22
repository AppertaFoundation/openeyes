describe('intraocular pressure element behaviour', () => {
    let patient, createEventUrl;

    before(() => {
        cy.login().then(() => {
            return cy.createPatient();
        }).then((newPatient) => {
            patient = newPatient;

            return cy.getEventCreationUrl(patient.id, 'OphCiExamination')
                .then((url) => {
                    createEventUrl = url;
                });
        });
    });

    it('only can add IOP instruments from the current institution, or those for all institutions', () => {
        cy.login().then(() => {
            return cy.runSeeder('OphCiExamination', 'IOPInstrumentsSeeder');
        }).then((seederData) => {
            cy.visit(createEventUrl);

            return cy.addExaminationElement('Intraocular Pressure').then((element) => {
                return [seederData, element];
            });
        }).then(([seederData, element]) => {
            //cy.getBySel('intraocular-pressure-element').find('.remove-side').first().click();
            cy.getBySel('add-intraocular-pressure-instrument').first().click().then(() => {
                cy.getBySel('add-options', '[data-id="instrument"]').find(`[data-id="${seederData.instrumentForCurrent.id}"]`);
                cy.getBySel('add-options', '[data-id="instrument"]').find(`[data-id="${seederData.instrumentForBoth.id}"]`);
                cy.getBySel('add-options', '[data-id="instrument"]').find(`[data-id="${seederData.instrumentForAll.id}"]`)
                cy.getBySel('add-options', '[data-id="instrument"]').find(`[data-id="${seederData.instrumentForOther.id}"]`).should('not.exist');
            });
        });
    });
});
