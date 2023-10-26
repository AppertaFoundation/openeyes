describe('create examination event', () => {
    const SETTING_NAME = 'require_pin_for_prescription';
    beforeEach(() => {
        cy.login()
            .then(() => {
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
            });
    });

    it(`add the medication management element`, function() {
        cy.setSystemSettingValue(SETTING_NAME, 'no');
        cy.removeElements();
        cy.addExaminationElement('Medication Management');
        cy.addExaminationElement('Anterior Segment');

        cy.getBySel('mm-add-standard-set-btn').scrollIntoView();
        cy.getBySel('mm-add-standard-set-btn').click();
        
        cy.getBySel('Medication-Management-element-section').within(() => {
            cy.getBySel('add-options').children().first().click();
            //Click on add button
            cy.getBySel('add-icon-btn').first().click().then((element) => {
                cy.intercept({
                    method: 'GET',
                    url: '/medicationManagement/getDrugSetForm*'
                }).as('drugSetForm');
            });
        });

        cy.wait('@drugSetForm');

        cy.getBySel('event-medication-management-row').each(($el) => {
            cy.get($el).within(() => {
                cy.getBySel('eye-lat-input').first().scrollIntoView().click();
                cy.getBySel('dispense-condition').select(1);
            })
        });

        cy.getBySel('event-action-confirm-and-save', ':visible').first().click();
        /*
            If any uncaught exception is thrown at this stage, test will fail.
            There is no need to handle that scenario.
        */
    });

    after(() => {
        cy.resetSystemSettingValue(SETTING_NAME);
    });
})
