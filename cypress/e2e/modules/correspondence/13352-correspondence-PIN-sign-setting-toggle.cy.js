describe('verifies correspondence esign behaviour', () => {

    const REQUIRE_PIN_SIGN_SETTING = 'require_pin_for_correspondence'

    before(() => {
        // Seed a single user - return user (with atttributes username, password and fullName)
        cy.login()
            .then(() => {
                return cy.runSeeder('OphCoCorrespondence', 'CorrespondencePINSignSeeder')
            }).as('seederData')
    })

    it('ensures that the signature is auto signed when PIN is not required and that the signatory details are not overwritten', () => {

        // set Require PIN for Correspondence signing to NO
        cy.setSystemSettingValue(REQUIRE_PIN_SIGN_SETTING, 'no')

        // login as admin, create a patient, then create a correspondence event for said patient
        cy.login()
            .then(() => {
                return cy.createPatient()
            })
            .then((patient) => {
                return cy.getEventCreationUrl(patient.id, 'OphCoCorrespondence')
                    .then((url) => {
                        cy.visit(url)
                    })
            })

        // fill in the mandatory fields (first option - index 1 - is fine)
        cy.getBySel('letter-type').select(1)
        cy.getBySel('letter-to_0').select(1)
        cy.getBySel('letter-template').select(1)

        // assert that the signature field is visible (and that it is auto signed)
        cy.getBySel('signature-wrapper').scrollIntoView().should('be.visible')

        // save the draft event then recall the form
        cy.getBySel('event-action-save-draft').first().click()
            .then(() => {
                cy.getBySel('button-header-tab-Edit').click()
            })

        // store the event url and the values that we subsequently need to compare
        cy.url().as('eventUrl')
        cy.getBySel('signatory-name').scrollIntoView().invoke('text').as('signatoryName')
        cy.getBySel('signature-date').scrollIntoView().invoke('text').as('signatureDate')
        cy.getBySel('esigned-at').scrollIntoView().invoke('text').as('esignedAt')

        // log in as our seeded user and revisit the saved correspondence event
        cy.get('@seederData').then((data) => {

            cy.login(data.user.username, data.user.password)

            cy.get('@eventUrl').then((url) => {
                
                cy.visit(url)

                cy.get('@signatoryName').then((signame) => {
                    // assert that the signatory remains as admin (and has not been updated to the seeded/current logged in user)
                    cy.getBySel('signatory-name').scrollIntoView().invoke('text').should('eq', signame)
                })
                cy.get('@signatureDate').then((sigdate) => {
                    // assert that the signature date has not been updated
                    cy.getBySel('signature-date').scrollIntoView().invoke('text').should('eq', sigdate)
                })
                cy.get('@esignedAt').then((sigtime) => {
                    // assert that the esigned at timestamp has not been updated
                    cy.getBySel('esigned-at').scrollIntoView().invoke('text').should('eq', sigtime)
                })
  
            })

        })

    })

    it('ensures that the signature field is not visible when PIN is required for signing', () => {

        // set Require PIN for Correspondence signing to YES
        cy.setSystemSettingValue(REQUIRE_PIN_SIGN_SETTING, 'yes')

        // login as admin, create a patient, then create a correspondence event for said patient
        cy.login()
            .then(() => {
                 return cy.createPatient()
            })
            .then((patient) => {
                return cy.getEventCreationUrl(patient.id, 'OphCoCorrespondence')
                    .then((url) => {
                        cy.visit(url)
                    })
            })

        // assert that the PIN sign button is visible (and thus the signature field is not)
        cy.getBySel('pin-sign-button').first().scrollIntoView().should('be.visible')

     })

})