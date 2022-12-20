Cypress.Commands.add('populatePatientBiometryData', (event_id) => {
    cy.createModels('OphInBiometry_Measurement', [['forSubspecialtyIds', [context.body.subspecialty_id], context.body.institution_id]], {active: 0});
})