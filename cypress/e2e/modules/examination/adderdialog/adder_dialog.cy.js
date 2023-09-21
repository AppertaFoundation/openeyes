describe('adder dialog tests', () => {
    describe('toggle by column id shows correct options', () => {
        beforeEach(() => {
            cy.login()
                .then(() => {
                    return cy.createPatient();
                })
                .then((patient) => {
                    return cy.getEventCreationUrl(patient.id, 'OphCiExamination')
                        .then((url) => {
                            return [url];
                        });
                })
                .then(([url]) => {
                    cy.visit(url);
                    cy.addExaminationElement('Intraocular Pressure');
                    cy.addExaminationElement('IOP History');
                    cy.addExaminationElement('DR Retinopathy');
                    cy.addExaminationElement('Triage');
                });
        });

        it('shows correct options on create and ', () => {
            for (let side of ['left', 'right']) {
                cy.dialogCorrectOptionsShownOnOpenAndAfterOptionsSelected('intraocular-pressure-element',
                    'add-intraocular-pressure-instrument',
                    ['instrument', 'reading_value'], ['scale_value'], ['Palpation'],
                    ['instrument', 'scale_value'], ['reading_value'],
                    side);

                cy.dialogCorrectOptionsShownOnOpenAndAfterOptionsSelected('iop-history-element',
                    'add-iop-history-instrument',
                    ['instrument', 'reading_value', 'time'], ['scale_value'], ['Palpation'],
                    ['instrument', 'scale_value', 'time'], ['reading_value'],
                    side);

                cy.dialogCorrectOptionsShownOnOpenAndAfterOptionsSelected('DR-Retinopathy-element-section',
                    `add-to-dr-retinopathy-${side}-btn`,
                    [`add-to-retinopathy-dr-${side}`],
                    [
                        `add-to-retinopathy-ma-${side}`,
                        `add-to-retinopathy-r1-${side}`,
                        `add-to-retinopathy-r2-${side}`,
                        `add-to-retinopathy-r3s-${side}`,
                        `add-to-retinopathy-r3a-${side}`
                    ],
                    ['DR', 'MA'],
                    [
                        `add-to-retinopathy-dr-${side}`,
                        `add-to-retinopathy-ma-${side}`,
                        `add-to-retinopathy-r1-${side}`,
                        `add-to-retinopathy-r2-${side}`,
                        `add-to-retinopathy-r3s-${side}`,
                        `add-to-retinopathy-r3a-${side}`
                    ], [],
                    side);
            }

            cy.dialogCorrectOptionsShownOnOpenAndAfterOptionsSelected('Triage-element-section',
                'add-chief-complaint',
                ['chief_complaint_id', 'eye_id'], ['eye_injury_id'], ['Eye injury'],
                ['chief_complaint_id', 'eye_id', 'eye_injury_id'], []);
        });
    });
});
