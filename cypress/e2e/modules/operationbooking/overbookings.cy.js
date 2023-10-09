describe('verifies operation booking capacity functionality', () => {

    const TODAY = new Date()

    const ADD_OP_BOOKING_SESSION_URL              = '/OphTrOperationbooking/admin/addSession'
    const OP_BOOKING_SESSION_CONTEXT              = '1 Stop Cataract (Cataract)'
    const OP_BOOKING_SESSION_THEATRE              = 'Ophthalmology Theatre'
    const OP_BOOKING_SESSION_START_TIME           = TODAY.toLocaleTimeString().substring(0, 8).trim()
    const OP_BOOKING_SESSION_END_TIME             = TODAY.toLocaleTimeString().substring(0, 8).trim()
    const OP_BOOKING_SESSION_MAX_PROCEDURES       = '1'
    const OP_BOOKING_SESSION_MAX_COMPLEX_BOOKINGS = '0'

    before(() => {
        
        // login as admin
         cy.login()

        // create an operation booking session for TODAY (this second) with max procedures = 1 and max complex bookings = 0 (to ensure repeatability)
        cy.visit(ADD_OP_BOOKING_SESSION_URL)
        cy.getBySel('session-context').select(OP_BOOKING_SESSION_CONTEXT)
        cy.getBySel('session-theatre').select(OP_BOOKING_SESSION_THEATRE)
        cy.getBySel('session-start-time').type(OP_BOOKING_SESSION_START_TIME)
        cy.getBySel('session-end-time').type(OP_BOOKING_SESSION_END_TIME)
        cy.getBySel('session-max-procedures').type(OP_BOOKING_SESSION_MAX_PROCEDURES)
        cy.getBySel('session-max-complex-bookings').type(OP_BOOKING_SESSION_MAX_COMPLEX_BOOKINGS)
        cy.saveEvent()
        
    })

    it('ensures that valid warnings are displayed when max number of complex bookings/procedures are exceeded', () => {

        const OP_BOOKING_DIAGNOSIS = 'Cortical cataract'
        const OP_BOOKING_PROCEDURE = 'Phacoemulsification and Intraocular lens'

        // create an (adult) patient then create an operation booking for said patient
        cy.createPatient(['adult'])
            .then((patient) => {
                cy.getEventCreationUrl(patient.id, 'OphTrOperationbooking')
                    .then((url) => {
                        cy.visit(url)
                    })
                })

        // complete the operation booking with HIGH complexity (plus all other mandatory values)
        cy.getBySel('add-diagnosis-btn').click()
        cy.selectAdderDialogOptionText(OP_BOOKING_DIAGNOSIS)
        cy.confirmAdderDialog()

        cy.getBySel('add-procedure-btn').click()
        cy.selectAdderDialogOptionText(OP_BOOKING_PROCEDURE)
        cy.confirmAdderDialog()

        cy.getBySel('op-complexity',          '[value="10"]').check() // High
        cy.getBySel('op-anaesthetic-type',    '[value="7"]').check()  // No Anaesthetic
        cy.getBySel('op-anaesthetic-choice',  '[value="1"]').check()  // Patient preference
        cy.getBySel('op-stop-medication',     '[value="0"]').check()  // No
        cy.getBySel('op-schedule-options',    '[value="2"]').check()  // AM
        cy.getBySel('op-pre-assessment-type', '[value="1"]').check()  // None

        // save and schedule now and assert that the operation booking has been saved
        cy.getBySel('event-action-save-and-schedule-now').first().click()
            .then(() => {
                cy.assertEventSaved()
            })

        // ensure that the context matches that of our operation booking session
        cy.getBySel('firm-switcher').select(OP_BOOKING_SESSION_CONTEXT)

        // 'select' a theatre slot for our session date - defaulted to TODAY
        cy.url()
            .then((url) => {
                cy.visit(url + '&day=' + TODAY.getDate())
            })

        // assert that the latest theatre slot has 1 procedure and 0 complex bookings available as expected
        cy.getBySel('available-procedures').last().should('contain', '1 Procedure(s) available')
        cy.getBySel('available-complex-bookings').last().should('contain', '0 Complex Booking(s) available')

        // select the latest time and make the first assertion
        cy.getBySel('select-time').last().click()
        cy.getBySel('session-unavailable-warning').last().should('contain', 'The allowed number of complex bookings has been reached for this session')

        // confirm slot and assert again (i.e. the complex booking limit warning shows if the booking is high complexity AND would execeed the number of configured complex bookings for the chosen slot)
        cy.getBySel('confirm-slot').click()
        cy.getBySel('complex-bookings-warning').children().should('contain', 'The allowed number of complex bookings has already been reached for this session. Are you sure you want to add another complex booking?')
  
        // schedule the booking nonetheless and assert
        cy.getBySel('dialog-ok-button').click()
        cy.getBySel('event-title').should('contain', 'Operation booking (Scheduled)')

        // create a second (adult) patient then create an operation booking for said patient
        cy.createPatient(['adult'])
            .then((patient) => {
                cy.getEventCreationUrl(patient.id, 'OphTrOperationbooking')
                    .then((url) => {
                        cy.visit(url)
                    })
                })
        
        // complete the operation booking with LOW complexity (plus all other mandatory values)
        cy.getBySel('add-diagnosis-btn').click()
        cy.selectAdderDialogOptionText(OP_BOOKING_DIAGNOSIS)
        cy.confirmAdderDialog()

        cy.getBySel('add-procedure-btn').click()
        cy.selectAdderDialogOptionText(OP_BOOKING_PROCEDURE)
        cy.confirmAdderDialog()

        cy.getBySel('op-complexity',          '[value="0"]').check() // Low
        cy.getBySel('op-anaesthetic-type',    '[value="7"]').check() // No Anaesthetic
        cy.getBySel('op-anaesthetic-choice',  '[value="1"]').check() // Patient preference
        cy.getBySel('op-stop-medication',     '[value="0"]').check() // No
        cy.getBySel('op-schedule-options',    '[value="2"]').check() // AM
        cy.getBySel('op-pre-assessment-type', '[value="1"]').check() // None

        // save and schedule now and assert that the operation booking has been saved
        cy.getBySel('event-action-save-and-schedule-now').first().click()
            .then(() => {
                cy.assertEventSaved()
            })

        // ensure that the context matches that of our operation booking session
        cy.getBySel('firm-switcher').select(OP_BOOKING_SESSION_CONTEXT)

        // 'select' a theatre slot for our session date - defaulted to TODAY
        cy.url()
            .then((url) => {
                cy.visit(url + '&day=' + TODAY.getDate())
            })

        // assert that the latest theatre slot has 0 procedure and -1 complex bookings available
        cy.getBySel('available-procedures').last().should('contain', '0 Procedure(s) available')
        cy.getBySel('available-complex-bookings').last().should('contain', '-1 Complex Booking(s) available')

        // select the latest time, assert that the max number of procedures has been exceeded and that the 'Confirm slot' button is not present
        cy.getBySel('select-time').last().click()
        cy.getBySel('session-unavailable-warning').last().should('contain', 'This operation has too many procedures for this session')
        cy.getBySel('confirm-slot').should('not.exist')

    })

})
