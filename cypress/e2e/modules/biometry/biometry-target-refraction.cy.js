describe('biometry target refraction warning tests', () => {
    beforeEach(() => {
        cy.login();
    });

    it('exam from 2 years or more in the past is not bringing value in', () => {
        let patientId;

        //It's a hardcoded date, but we only care that it is in the past
        const previousEventDate = '2021-08-20';
        const oldTargetRefractionValue = '2.00';
        const eyeId = 1;
        const side = 'left';
        cy.runSeeder('OphCiExamination', 'CataractSurgicalManagementSeeder',
            {
                'eye_id': eyeId,
                'target_refraction': oldTargetRefractionValue,
                'event_date': previousEventDate
            }).then((seederData) => {
            patientId = seederData.event.patient.id;
            cy.runSeeder('OphInBiometry', 'PopulatedTargetRefractionBiometrySeeder',
                {'patient_id': patientId, 'eye_id': eyeId})
                .then((body) => {
                    cy.visit(body.event.urls.edit);

                    const targetRefraction = cy.getBySel(`${side}-target-refraction`);
                    targetRefraction.should('not.have.value', oldTargetRefractionValue);

                    cy.getBySel(`${side}-refractive-target-warning`).should('be.hidden');
                });
        });
    });

    it('warning functionality when Cataract Surgical Management exists and have value', () => {
        let patientId;
        const latestTargetRefractionValue = '3.00';
        const manuallyOverridenRefractionValue = '2.00';
        const eyeId = 1;
        const side = 'left';

        cy.runSeeder('OphCiExamination', 'CataractSurgicalManagementSeeder',
            {
                'eye_id': eyeId,
                'target_refraction': latestTargetRefractionValue,
            }).then((seederData) => {
            patientId = seederData.event.patient.id;

            cy.runSeeder('OphInBiometry', 'PopulatedTargetRefractionBiometrySeeder',
                {'patient_id': patientId, 'eye_id': eyeId})
                .then((body) => {
                    cy.visit(body.event.urls.edit);

                    cy.getBySel(`${side}-target-refraction`)
                        .should('have.value', latestTargetRefractionValue);

                    cy.getBySel(`${side}-refractive-target-warning`).should('be.hidden');

                    cy.get(`#${side}-eye-selection`).scrollIntoView();
                    cy.getBySel(`${side}-lens-dropdown`).filter(':visible').select(1);
                    cy.saveEvent();

                    let targetRefraction = cy.getBySel(`${side}-target-refraction`);
                    targetRefraction.should('have.value', latestTargetRefractionValue);

                    let refractionTargetWarning = cy.getBySel(`${side}-refractive-target-warning`);

                    refractionTargetWarning.should('be.hidden');

                    cy.refractionWarningAssertionWithTwoValuesForSide(side,
                        [latestTargetRefractionValue, 'be.not.visible'],
                        [manuallyOverridenRefractionValue, 'be.visible'],
                        latestTargetRefractionValue
                    );

                    cy.get(`#${side}-eye-selection`).scrollIntoView();
                    cy.getBySel(`${side}-lens-dropdown`).filter(':visible').select(0);

                    cy.saveEvent();

                    cy.getBySel(`${side}-target-refraction`).should('contain.text',
                        manuallyOverridenRefractionValue);

                    cy.get('div.alert-box.issue').contains('Left Target differs from Cataract Surgical Management target of 3.00').should('be.visible');
                });
        });
    });

    it('warning functionality when Cataract Surgical Management does not exist', () => {
        const eyeId = 2;
        const side = 'right';
        cy.runSeeder('OphInBiometry', 'PopulatedTargetRefractionBiometrySeeder',
            {'eye_id': eyeId})
            .then((body) => {
                cy.visit(body.event.urls.edit);

                let targetRefraction = cy.getBySel(`${side}-target-refraction`);
                targetRefraction.should('have.value', '');

                let refractionTargetWarning = cy.getBySel(`${side}-refractive-target-warning`);

                refractionTargetWarning.should('be.hidden');

                cy.refractionWarningAssertionWithTwoValuesForSide(side,
                    [-3.00, 'be.not.visible'],
                    [2.15, 'be.not.visible'],
                    null
                );
            });
    });

    it('warning functionality when Cataract Surgical Management exist , but has no value', () => {
        const eyeId = 2;
        const side = 'right';
        let patientId;

        cy.runSeeder('OphCiExamination', 'CataractSurgicalManagementSeeder',
            {'eye_id': eyeId}).then((seederData) => {
            patientId = seederData.event.patient.id;
            cy.runSeeder('OphInBiometry', 'PopulatedTargetRefractionBiometrySeeder',
                {'patient_id': patientId, 'eye_id': eyeId})
                .then((body) => {
                    cy.visit(body.event.urls.edit);

                    let targetRefraction = cy.getBySel(`${side}-target-refraction`);
                    targetRefraction.should('have.value', '');

                    let refractionTargetWarning = cy.getBySel(`${side}-refractive-target-warning`);

                    refractionTargetWarning.should('be.hidden');

                    cy.get(`#${side}-eye-selection`).scrollIntoView();
                    cy.getBySel(`${side}-lens-dropdown`).filter(':visible').select(1);
                    cy.saveEvent();

                    targetRefraction = cy.getBySel(`${side}-target-refraction`);
                    targetRefraction.should('have.value', '');

                    cy.refractionWarningAssertionWithTwoValuesForSide(side,
                        [-1.00, 'be.not.visible'],
                        [0.00, 'be.not.visible'],
                        null
                    );
                });
        });
    });

    it('populates value when the target refraction is empty only when going from view to edit mode', () => {
        let patientId;
        const latestTargetRefractionValue = '2.00';
        const eyeId = 1;
        const side = 'left';

        cy.runSeeder('OphCiExamination', 'CataractSurgicalManagementSeeder',
            {
                'eye_id': eyeId,
                'target_refraction': latestTargetRefractionValue,
            }).then((seederData) => {
            patientId = seederData.event.patient.id;

            cy.runSeeder('OphInBiometry', 'PopulatedTargetRefractionBiometrySeeder',
                {'patient_id': patientId, 'eye_id': eyeId})
                .then((body) => {
                    cy.visit(body.event.urls.edit);

                    cy.getBySel(`${side}-target-refraction`).clear();

                    cy.get(`#${side}-eye-selection`).scrollIntoView();
                    cy.getBySel(`${side}-lens-dropdown`).filter(':visible').select(1);
                    cy.saveEvent();

                    let targetRefraction = cy.getBySel(`${side}-target-refraction`);
                    targetRefraction.should('not.have.value', latestTargetRefractionValue);

                    cy.get(`#${side}-eye-selection`).scrollIntoView();
                    cy.getBySel(`${side}-lens-dropdown`).filter(':visible').select(0);
                    cy.saveEvent();

                    cy.getBySel(`${side}-target-refraction`).should('not.contain.text',
                        latestTargetRefractionValue);

                    cy.get('div.alert-box.issue').should('not.exist');

                    cy.getBySel('button-event-header-tab-edit').click();

                    targetRefraction = cy.getBySel(`${side}-target-refraction`);
                    targetRefraction.should('have.value', latestTargetRefractionValue);
                });
        });
    });


});