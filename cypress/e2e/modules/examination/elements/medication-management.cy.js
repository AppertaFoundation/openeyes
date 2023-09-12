describe('test suite to verify medication management functionality', () => {

    const REQUIRE_PIN_SIGN_SETTING = 'require_pin_for_prescription'
    const SAVE_AND_DISCARD_SETTING = 'close_incomplete_exam_elements'
    const CONTEXT                  = 'Follow-up (Cataract)'
    const DRUG_OPTIONS             = 'Both'
    const DRUG_FREQUENCY           = 'when needed'
    const DRUG_DURATION            = 'Ongoing'
    const DRUG_DISPENSE_CONDITION  = 'Hospital to supply'
    const MEDS_STOP_REASON         = 'No longer required'

    before(() => {

        // set Require PIN for Prescription signing to NO
        cy.setSystemSettingValue(REQUIRE_PIN_SIGN_SETTING, 'no')

        // set Offer to automatically close incomplete examination elements to OFF
        cy.setSystemSettingValue(SAVE_AND_DISCARD_SETTING, 'off')

        // Seed:
        // - a single non-admin user - return user (with atttributes username and password)
        // - a first common ophthalmic drug with route 'Eye' - return drug1 array
        // - a second ophthalmic drug with route 'Eye' - return drug2 array
        cy.login()
            .then(() => {
                return cy.runSeeder('OphCiExamination', 'MedicationManagementSeeder')
            }).as('seederData')

    })

    it('ensures that the same drug stopped in Medication Management is stopped and disabled in Medication History', () => {

        cy.get('@seederData').then((data) => {    

            // log in as our seeded user and change context to 'Follow-up (Cataract)'
            cy.login(data.user.username, data.user.password)
            cy.visit('/')
            cy.getBySel('change-firm').click()
            cy.getBySel('change-firm-site-context-popup').select(CONTEXT)
            cy.getBySel('confirm-change-site-context-popup').click()
        
            // create a patient, then add a prescription event for said patient
            cy.createPatient()
                .then((patient) => {
                    return cy.getEventCreationUrl(patient.id, 'OphDrPrescription')
                        .then((url) => {                   
                            cy.visit(url)   
                            // store the patient id (to create a subsequent examination event for the same patient)
                            cy.wrap(patient.id).as('patientId')                       
                        })
                })

            // select our 2 (different) ophthalmic drugs ...
            cy.getBySel('add-prescription-button').click()
            cy.selectAdderDialogOptionText(data.drug1.name)
            cy.selectAdderDialogOptionText(data.drug2.name)
            cy.confirmAdderDialog()

            // both eyes ...
            cy.getBySel('route-option').each(($el) => {
                cy.wrap($el).select(DRUG_OPTIONS)
            })

            // when needed ...
            cy.getBySel('drug-frequency').each(($el) => {
                cy.wrap($el).select(DRUG_FREQUENCY)
            })

            // ongoing duration ...
            cy.getBySel('drug-duration').each(($el) => {
                cy.wrap($el).select(DRUG_DURATION)
            })

            // hospital to supply ...
            cy.getBySel('drug-dispense-condition').each(($el) => {
                cy.wrap($el).select(DRUG_DISPENSE_CONDITION)
            })

            // *** ASSUMPTION: all other prescription values are pre-populated

            // save the prescription event and assert that it is so
            cy.saveEvent()
                .then(() => {
                    cy.assertEventSaved()
                })

            // create an examination event for the same patient
            cy.get('@patientId')
                .then((patientid) => {
                    return cy.getEventCreationUrl(patientid, 'OphCiExamination')
                        .then((url) => {
                            cy.visit(url)
                        })
                })

            // remove all elements and add only Medication History and Medication Management elements
            cy.removeElements()
            cy.addExaminationElement('Medication History')
            cy.addExaminationElement('Medication Management')

            // in Medication Management ...
            cy.getBySel('Medication-Management-element-section').within(() => {

                // get the data key of our first medication, stop the med and select a reason for stopping
                cy.getBySel('medication-name').contains(data.drug1.name).parents('tr').invoke('attr', 'data-key')
                    .then((datakey) => {
                        cy.getBySel('meds-stop-btn-' + datakey).click()
                        cy.getBySel('meds-stop-reason-' + datakey).select(MEDS_STOP_REASON)
                    })

            })

            // in Medication History ...
            cy.getBySel('Medication-History-element-section').within(() => {

                // get the data key of our first medication ...
                cy.getBySel('medication-name').contains(data.drug1.name).parents('tr').invoke('attr', 'data-key')
                    .then((datakey) => {
                    
                        // assert that the first of the meds is stopped and disabled
                        cy.getBySel('stopped-btn-' + datakey).should('not.be.visible')
                        cy.getBySel('event-medication-history-row-' + datakey).should('have.class', 'fade').and('have.class', 'disabled')
                    })

                // get the data key of our second medication ...
                cy.getBySel('medication-name').contains(data.drug2.name).parents('tr').invoke('attr', 'data-key')
                    .then((datakey) => {
                    
                        // assert that the second of the meds is NOT stopped and disabled; i.e., it remains active
                        cy.getBySel('stopped-btn-' + datakey).should('be.visible')
                        cy.getBySel('event-medication-history-row-' + datakey).should('not.have.class', 'fade').and('not.have.class', 'disabled')
                    })
           
            })

        })

        // confirm & save and assert
        cy.saveEvent()
            .then(() => {
                cy.assertEventSaved()
            })

    })

})