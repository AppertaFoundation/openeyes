describe('biometry predicted and target refraction warning tests', () => {

    beforeEach(() => {
        cy.login();
    });

    it('Warning is shown in op note when predicted refraction is different from  cataract surgical management target ', () => {
        let patientId;
        const predictedRefraction = '3.00';
        const targetRefraction = '2.00';
        const eyeId = 1;
        const side = 'left';

        cy.runSeeder('OphInBiometry', 'PopulatedTargetRefractionBiometrySeeder',
            {
                'eye_id': eyeId,
                'target_refraction': targetRefraction, 'predicted_refraction': predictedRefraction
            })
            .then((seederData) => {
                patientId = seederData.event.patient_id;
                cy.getEventCreationUrl(patientId, 'OphTrOperationnote')
                    .then((url) => {
                        cy.visit(url);
                    });

                cy.contains('Create default op note').click();
                cy.fixture('13040-operation-note-templates')
                    .then((fixture) => {
                        cy.fillOperationNote(fixture.templateData).then(() => {
                            cy.getBySel(`${side}_target_refraction_warning_tooltip`).should('exist')
                                .invoke('attr', 'data-tooltip-content')
                                .should('contain', 'Warning: Predicted refraction of the chosen lens is more than 0.5D out from target refraction.');
                        });
                    });
            });
    });

    it('Warning is not shown in op note when predicted refraction is the same from cataract surgical management target',
        () => {
            let patientId;
            const predictedRefraction = '1.50';
            const targetRefraction = '1.00';
            const eyeId = 1;
            const side = 'left';

            cy.runSeeder('OphInBiometry', 'PopulatedTargetRefractionBiometrySeeder',
                {
                    'eye_id': eyeId,
                    'target_refraction': targetRefraction, 'predicted_refraction': predictedRefraction
                })
                .then((seederData) => {
                    patientId = seederData.event.patient_id;
                    cy.getEventCreationUrl(patientId, 'OphTrOperationnote')
                        .then((url) => {
                            cy.visit(url);
                        });

                    cy.contains('Create default op note').click();
                    cy.fixture('13040-operation-note-templates')
                        .then((fixture) => {
                            cy.fillOperationNote(fixture.templateData).then(() => {
                                cy.getBySel(`${side}_target_refraction_warning_tooltip`).should('not.exist');
                            });
                        });
                });
        });

    it('target and predicted refraction test with predicted refraction values which are under 0.5 and over 0.5 target refraction', () => {
        const targetRefraction = '-1';
        const eyeId = 1;
        const side = 'left';
        cy.runSeeder('OphInBiometry', 'PopulatedTargetRefractionBiometrySeeder',
            {'eye_id': eyeId, 'target_refraction': targetRefraction})
            .then((body) => {
                cy.visit(body.event.urls.edit);


                cy.get(`#${side}-eye-selection`).within(() => {
                    cy.getBySel(`${side}-lens-dropdown`).filter(':visible').select(1);
                    cy.getBySel(`${side}-formula-dropdown`).filter(':visible').select(1);

                    cy.get('.iol-ref-value').contains('-1.87').closest('tr').find('.iolrefselection').click();
                    cy.getBySel(`${side}-refractive-predicted-target-warning`).should('be.visible')
                        .should('contain.text',
                            'Warning: Predicted refraction of the chosen lens is more than 0.5D out from target refraction.');

                    cy.get('.iol-ref-value').contains('-1.17').closest('tr').find('.iolrefselection').click();
                    cy.getBySel(`${side}-refractive-predicted-target-warning`).should('be.not.visible');

                    cy.get('.iol-ref-value').contains('-1.87').closest('tr').find('.iolrefselection').click();
                    cy.getBySel(`${side}-refractive-predicted-target-warning`).should('be.visible');

                });

                cy.saveEvent();

                cy.get('div.alert-box.issue').contains('Left Predicted refraction of the chosen lens is more than 0.5D out from target refraction.').should('be.visible');
                cy.getBySel('button-event-header-tab-edit').click();

                cy.get(`#${side}-eye-selection`).scrollIntoView().within(() => {
                    cy.getBySel(`${side}-refractive-predicted-target-warning`).should('be.visible');
                    cy.get('.iol-ref-value').contains('-1.17').closest('tr').find('.iolrefselection').click();
                    cy.getBySel(`${side}-refractive-predicted-target-warning`).should('be.not.visible');

                });

                cy.saveEvent();
                cy.get('div.alert-box.issue').should('not.exist');
                cy.getBySel('button-event-header-tab-edit').click();

                cy.getBySel(`${side}-refractive-predicted-target-warning`).should('be.not.visible');
            });
    });

    it('target and predicted refraction test with predicted refraction values when target refraction is empty', () => {
        const eyeId = 1;
        const side = 'left';
        cy.runSeeder('OphInBiometry', 'PopulatedTargetRefractionBiometrySeeder',
            {'eye_id': eyeId})
            .then((body) => {
                cy.visit(body.event.urls.edit);

                cy.get(`#${side}-eye-selection`).within(() => {
                    cy.getBySel(`${side}-lens-dropdown`).filter(':visible').select(1);
                    cy.getBySel(`${side}-formula-dropdown`).filter(':visible').select(1);

                    cy.get('.iol-ref-value').contains('-2.96').closest('tr').find('.iolrefselection').click();
                    cy.getBySel(`${side}-refractive-predicted-target-warning`).should('be.not.visible');

                    cy.get('.iol-ref-value').contains('-1.17').closest('tr').find('.iolrefselection').click();
                    cy.getBySel(`${side}-refractive-predicted-target-warning`).should('be.not.visible');

                    cy.get('.iol-ref-value').contains('-2.96').closest('tr').find('.iolrefselection').click();
                    cy.getBySel(`${side}-refractive-predicted-target-warning`).should('be.not.visible');
                });

                cy.saveEvent();
                cy.getBySel('button-event-header-tab-edit').click();

                cy.get(`#${side}-eye-selection`).scrollIntoView().within(() => {
                    cy.getBySel(`${side}-refractive-predicted-target-warning`).should('be.not.visible');
                    cy.get('.iol-ref-value').contains('-1.17').closest('tr').find('.iolrefselection').click();
                    cy.getBySel(`${side}-refractive-predicted-target-warning`).should('be.not.visible');

                });

                cy.saveEvent();
                cy.getBySel('button-event-header-tab-edit').click();

                cy.getBySel(`${side}-refractive-predicted-target-warning`).should('be.not.visible');
            });
    });
});