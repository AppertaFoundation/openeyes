describe('verifies prescription esign behaviour', () => {

    const REQUIRE_PIN_SIGN_SETTING = 'require_pin_for_prescription'

    before(() => {
        // Seed a single user - return user (with atttributes username, password and fullName)
        cy.login()
            .then(() => {
                return cy.runSeeder('OphDrPrescription', 'PrescriptionPINSignSeeder')
            }).as('seederData')
    })

    it('ensures that the signature is auto signed when PIN is not required and that the signatory details are not overwritten', () => {

        // set Require PIN for Prescription signing to NO
        cy.setSystemSettingValue(REQUIRE_PIN_SIGN_SETTING, 'no')

        // login as admin, create a patient, then create a prescription event for said patient
        cy.login()
            .then(() => {
                return cy.createPatient()
            })
            .then((patient) => {
                return cy.getEventCreationUrl(patient.id, 'OphDrPrescription')
                    .then((url) => {
                        cy.visit(url)
                    })
            })

        // assert that the auto signature is visible
        cy.getBySel('signature-wrapper').should('be.visible')
            
        // enter minimal prescription details
        cy.getBySel('add-standard-set-button').click()
        cy.selectAdderDialogOptionText('Post-op')
        cy.confirmAdderDialog()

        cy.getBySel('route-option').each(($el) => {
            cy.wrap($el).select(1)
        })

        // assert that the auto signature is still visible (?)
        cy.getBySel('signature-wrapper').should('be.visible')

        // save the prescription
        cy.getBySel('event-action-save').first().click()

        // store the event url and the values that we subsequently need to compare
        cy.url().as('eventUrl')
        cy.getBySel('signatory-name').invoke('text').as('signatoryName')
        cy.getBySel('signature-date').invoke('text').as('signatureDate')
        cy.getBySel('esigned-at').invoke('text').as('esignedAt')

        // edit the prescription (giving reason)
        cy.getBySel('button-event-header-tab-edit').click()
        cy.getBySel('reason_2').click()

        // assert that the auto signature is still visible (??)
        cy.getBySel('signature-wrapper').should('be.visible')

        // log in as our seeded user and revisit the saved prescription event
        cy.get('@seederData').then((data) => {

            cy.login(data.user.username, data.user.password)

            cy.get('@eventUrl').then((url) => {
                
                cy.visit(url)

                cy.get('@signatoryName').then((signame) => {
                    // assert that the signatory remains as admin (and has not been updated to the seeded/current logged in user)
                    cy.getBySel('signatory-name').invoke('text').should('eq', signame)
                })
                cy.get('@signatureDate').then((sigdate) => {
                    // assert that the signature date has not been updated
                    cy.getBySel('signature-date').invoke('text').should('eq', sigdate)
                })
                cy.get('@esignedAt').then((sigtime) => {
                    // assert that the esigned at timestamp has not been updated
                    cy.getBySel('esigned-at').invoke('text').should('eq', sigtime)
                })
  
            })

        })

    })

    it('ensures that the signature field is not visible when PIN is required for signing', () => {

        // set Require PIN for Prescription signing to YES
        cy.setSystemSettingValue(REQUIRE_PIN_SIGN_SETTING, 'yes')

        // login as admin, create a patient, then create a CVI event for said patient
        cy.login()
            .then(() => {
                return cy.createPatient()
            })
            .then((patient) => {
                return cy.getEventCreationUrl(patient.id, 'OphDrPrescription')
                    .then((url) => {
                        cy.visit(url)
                    })
            })

        // assert that the PIN sign button is visible (and thus the signature field is not)
        cy.getBySel('pin-sign-button').should('be.visible')

    })

})
