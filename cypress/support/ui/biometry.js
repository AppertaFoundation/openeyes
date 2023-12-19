Cypress.Commands.add('populatePatientBiometryData', (event_id) => {
    cy.createModels('OphInBiometry_Measurement', [['forSubspecialtyIds', [context.body.subspecialty_id], context.body.institution_id]], {active: 0});
})

Cypress.Commands.add('refractionWarningAssertionWithTwoValuesForSide', (side,
                                                                        firstValueWithVisibilityValue,
                                                                        secondValueWithVisibilityValue,
                                                                        latestTargetRefractionValue) => {

    let firstValue, firstVisibilityValue, secondValue, secondVisibilityValue;
    [firstValue, firstVisibilityValue] = firstValueWithVisibilityValue;
    [secondValue, secondVisibilityValue] = secondValueWithVisibilityValue;

    let targetRefraction = cy.getBySel(`${side}-target-refraction`);
    let refractionTargetWarning = cy.getBySel(`${side}-refractive-target-warning`);

    refractionTargetWarning.should('be.hidden');

    targetRefraction.clear().type(secondValue);
    targetRefraction.trigger('change');

    refractionTargetWarning = cy.getBySel(`${side}-refractive-target-warning`);


    cy.refractionWarningVisiblityAndTextChecker(secondVisibilityValue, latestTargetRefractionValue, refractionTargetWarning);

    targetRefraction.clear().type(firstValue);
    targetRefraction.trigger('change');

    refractionTargetWarning = cy.getBySel(`${side}-refractive-target-warning`);

    cy.refractionWarningVisiblityAndTextChecker(firstVisibilityValue, latestTargetRefractionValue, refractionTargetWarning);


    targetRefraction.clear().type(secondValue);
    targetRefraction.trigger('change');

    refractionTargetWarning = cy.getBySel(`${side}-refractive-target-warning`);

    cy.refractionWarningVisiblityAndTextChecker(secondVisibilityValue, latestTargetRefractionValue, refractionTargetWarning);


});

Cypress.Commands.add('refractionWarningVisiblityAndTextChecker', (visibilityValue, latestTargetRefractionValue,
                                                      refractionTargetWarningElement) => {

    refractionTargetWarningElement.should(visibilityValue);
    if (visibilityValue === 'be.visible') {
        refractionTargetWarningElement.should('include.text',
            `Target differs from Cataract Surgical Management target`);
        refractionTargetWarningElement.should('include.text', latestTargetRefractionValue);
    }
});