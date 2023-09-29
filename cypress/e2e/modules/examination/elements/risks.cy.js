describe('risks element behaviour', () => {
    it('Risks not checked checkbox is unchecked on edit', () => {
        cy.login()
            .then((context) => {
                cy.runSeeder(
                    'OphCiExamination',
                    'RiskSetSeeder',
                    {
                        'subspecialty_id': context.body.subspecialty_id
                    });

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
                cy.removeElements();
                return cy.addExaminationElement('Risks');
            });

        cy.getBySel("risks_body", " tr").each($tr => {
            cy.wrap($tr).findBySel("not_checked").click();
        });

        cy.saveEvent();
        cy.getBySel("button-event-header-tab-edit").click();
        cy.getBySel("risks_body", " tr").each($tr => {
            cy.wrap($tr).findBySel("not_checked").should('not.be.checked');
        });
    });

    it('Risks not checked checkbox is checked when loading a draft', () => {
        cy.login()
            .then((context) => {
                cy.runSeeder(
                    'OphCiExamination',
                    'AutoSaveSeeder',
                    {
                        initial_firm_id: context.body.firm_id,
                        additional_elements: ["Risks"]
                    })
                    .then(function (data) {
                        cy.runSeeder(
                            'OphCiExamination',
                            'RiskSetSeeder',
                            {
                                'subspecialty_id': data.draft_subspecialty_id
                            });
                        cy.addElementsToDraftExamination(data.draft_id, ["Risks"]);
                        cy.visit(data.draft_update_url);
                        cy.getBySel("risks_body", " tr").each($tr => {
                            cy.wrap($tr).findBySel("not_checked").should('be.checked');
                        });
                    });
            });
    });
});