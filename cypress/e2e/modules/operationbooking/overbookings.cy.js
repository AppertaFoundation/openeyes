describe('verifies operation booking capacity functionality', () => {

    const TODAY = new Date()

    const ADD_OP_BOOKING_SESSION_URL              = '/OphTrOperationbooking/admin/addSession'
    const OP_BOOKING_SESSION_CONTEXT              = '1 Stop Cataract (Cataract)'
    const OP_BOOKING_SESSION_THEATRE              = 'Ophthalmology Theatre'
    const OP_BOOKING_SESSION_START_TIME           = '10:00'
    const OP_BOOKING_SESSION_END_TIME             = '12:00'
    const OP_BOOKING_SESSION_MAX_PROCEDURES       = '1'
    const OP_BOOKING_SESSION_MAX_COMPLEX_BOOKINGS = '0'   

    before(() => {
        
        // login as admin
        cy.login()
        
        // create an operation booking session with max procedures = 1 and max complex bookings = 0
        cy.visit(ADD_OP_BOOKING_SESSION_URL)
        cy.getBySel('session-context').select(OP_BOOKING_SESSION_CONTEXT)
        cy.getBySel('session-theatre').select(OP_BOOKING_SESSION_THEATRE)
        cy.getBySel('session-start-time').type(OP_BOOKING_SESSION_START_TIME)
        cy.getBySel('session-end-time').type(OP_BOOKING_SESSION_END_TIME)
        cy.getBySel('session-max-procedures').type(OP_BOOKING_SESSION_MAX_PROCEDURES)
        cy.getBySel('session-max-complex-bookings').type(OP_BOOKING_SESSION_MAX_COMPLEX_BOOKINGS)
        cy.get('#OphTrOperationbooking_Operation_Session_paediatric_1').check() // in case our randomly created patient(s) are children (*** TODO: getBySel)
        cy.saveEvent()

        // save the url to retrieve the session for deletion later
        cy.getBySel('filter-sessions').click()
        cy.getBySel('sessions-list').first().click()
        cy.url().as('sessionUrl')        
        
    })

    it('ensures that valid warnings are displayed when max number of complex bookings/procedures are exceeded', () => {

        const OP_BOOKING_DIAGNOSIS          = 'Cortical cataract'
        const OP_BOOKING_PROCEDURE          = 'Phacoemulsification and Intraocular lens'
        const OP_BOOKING_COMPLEXITY_HIGH    = 'High'
        const OP_BOOKING_COMPLEXITY_LOW     = 'Low'
        const OP_BOOKING_ANAESTHETIC_TYPE   = ' No Anaesthetic '
        const OP_BOOKING_ANAESTHETIC_CHOICE = 'Patient preference'
        const OP_BOOKING_STOP_MEDICATION    = 'No'
        const OP_BOOKING_SCHEDULE_OPTION    = 'AM'
        const OP_BOOKING_PRE_ASSESSMENT     = 'None'

        // create a patient then create an operation booking for said patient
        cy.createPatient()
            .then((patient) => {
                return cy.getEventCreationUrl(patient.id, 'OphTrOperationbooking')
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

        // *** TODO: getBySel
        cy.get('#Element_OphTrOperationbooking_Operation_complexity_10').check()                        // High
        cy.get('[id="Element_OphTrOperationbooking_Operation_AnaestheticType_No Anaesthetic"]').check() // No Anaesthetic
        cy.get('#Element_OphTrOperationbooking_Operation_anaesthetic_choice_id_1').check()              // Patient preference
        cy.get('#Element_OphTrOperationbooking_Operation_stop_medication_0').check()                    // No
        cy.get('#Element_OphTrOperationbooking_ScheduleOperation_schedule_options_id_2').check()        // AM
        cy.get('#Element_OphTrOperationbooking_PreAssessment_type_id_1').check()                        // None

        // save and schedule now and assert that the operation booking has been saved
        cy.getBySel('event-action-save-and-schedule-now').first().click()
            .then(() => {
                cy.assertEventSaved()
            })

        // 'select' a theatre slot for our session date - defaulted to today
        cy.url().as('scheduleUrl')
        cy.get('@scheduleUrl').then((url) => {
            cy.visit(url + '?day=' + TODAY.getDate())
        })

        // assert that the theatre slot has 1 procedure and 0 complex bookings available as expected
        cy.getBySel('available-procedures').should('contain', '1 Procedure(s) available')
        cy.getBySel('available-complex-bookings').should('contain', '0 Complex Booking(s) available')

        // select the time and make the first assertion
        cy.getBySel('select-time').click()
        cy.getBySel('session-unavailable-warning').should('contain', 'The allowed number of complex bookings has been reached for this session')

        // confirm slot and assert again (i.e. the complex booking limit warning shows if the booking is high complexity AND would execeed the number of configured complex bookings for the chosen slot)
        cy.getBySel('confirm-slot').click()
        cy.get('.oe-popup-content').should('contain', 'The allowed number of complex bookings has already been reached for this session. Are you sure you want to add another complex booking?') // *** TODO: getBySel
  
        // schedule the booking nonetheless, assert and save the url to retrieve the scheduled operation for cancellation later
        cy.get('[class="secondary small confirm ok"]').click() // *** TODO: getBySel
        cy.getBySel('event-title').should('contain', 'Operation booking (Scheduled)')
        cy.url().as('operationUrl')

        // create a second patient then create an operation booking for said patient
        cy.createPatient()
            .then((patient) => {
                return cy.getEventCreationUrl(patient.id, 'OphTrOperationbooking')
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

        // *** TODO: getBySel
        cy.get('#Element_OphTrOperationbooking_Operation_complexity_0').check()                         // Low
        cy.get('[id="Element_OphTrOperationbooking_Operation_AnaestheticType_No Anaesthetic"]').check() // No Anaesthetic
        cy.get('#Element_OphTrOperationbooking_Operation_anaesthetic_choice_id_1').check()              // Patient preference
        cy.get('#Element_OphTrOperationbooking_Operation_stop_medication_0').check()                    // No
        cy.get('#Element_OphTrOperationbooking_ScheduleOperation_schedule_options_id_2').check()        // AM
        cy.get('#Element_OphTrOperationbooking_PreAssessment_type_id_1').check()                        // None

        // save and schedule now and assert that the operation booking has been saved
        cy.getBySel('event-action-save-and-schedule-now').first().click()
            .then(() => {
                cy.assertEventSaved()
            })

        // 'select' a theatre slot for our session date - defaulted to today
        cy.url().as('scheduleUrl')
        cy.get('@scheduleUrl').then((url) => {
            cy.visit(url + '?day=' + TODAY.getDate())
        })

        // assert that the theatre slot has 0 procedure and -1 complex bookings available
        cy.getBySel('available-procedures').should('contain', '0 Procedure(s) available')
        cy.getBySel('available-complex-bookings').should('contain', '-1 Complex Booking(s) available') // *** possible candidate for a bug fix?

        // select the time, assert that the max number of procedures has been exceeded and that the 'Confirm slot' button is not present
        cy.getBySel('select-time').click()
        cy.getBySel('session-unavailable-warning').should('contain', 'This operation has too many procedures for this session')
        cy.getBySel('confirm-slot').should('not.exist')

    })

    // Further test steps for consideration:
    // - ensure that the complex overbooking warning does not show if the booking is high complexity AND max complex bookings is not set
    // - ensure that the complex overbooking warning does not show if the booking is high complexity AND max complex bookings is set but not exceeded
    // - ensure that the complex overbooking warning does not show if the booking is not high complexity AND max complex bookings is set and exceeded
    // - ensure that the complex overbooking warning does not show if the booking is not high complexity AND max complex bookings is not set
    // - ensure that the complex overbooking warning does not show if the booking is not high complexity AND max complex bookings is set but not exceeded

    const OP_BOOKING_CANCELLATION_REASON = 'Booked In Error'

    after(() => {

        // cancel the scheduled operation
        cy.get('@operationUrl').then((url) => {
            cy.visit(url)
        })
        cy.getBySel('event-action-cancel-operation').first().click()
        cy.getBySel('op-cancellation-reason').select(OP_BOOKING_CANCELLATION_REASON)
        cy.getBySel('cancel-operation').click()

        // delete the operation booking session (for test repeatability)
        cy.get('@sessionUrl').then((url) => {
            cy.visit(url)
        })
        cy.get('#et_delete').click() // *** TODO: getBySel
        cy.getBySel('remove-session').click()

        // go home
        cy.visit('/')
            
    })
    
})
