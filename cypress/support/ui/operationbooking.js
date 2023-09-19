Cypress.Commands.add('runBiometryAndOperationBookingSeedersAndVisitOpBooking',
    (eyeId, predictedRefraction, targetRefraction = null) => {

    const biometryData = {};

    if (eyeId !== null) {
        biometryData.eye_id = eyeId;
    }

    if (targetRefraction !== null) {
        biometryData.target_refraction = targetRefraction;
    }

    if (predictedRefraction !== null) {
        biometryData.predicted_refraction = predictedRefraction;
    }

    cy.runSeeder('OphInBiometry', 'PopulatedTargetRefractionBiometrySeeder', biometryData)
        .then((seederData) => {
            const patientId = seederData.event.patient_id;

            cy.fixture('13040-operation-note-templates')
                .then((fixture) => {
                    let procedureNames = Object.entries(fixture.templateData.elementData.procedures).map(proc => proc[0]);

                    cy.runSeeder('OphTrOperationbooking', 'OpBookingWithProceduresSeeder',
                        {procedure_names: procedureNames, patient_id: patientId}).then(data => {
                        cy.visit(data.event.urls.view);
                    });
                });
        });
});