describe('create a message via patient record', () => {
    
    before(() => {
        // Seed a single user - return user (with atttributes username, password, fullName and messageText)
        cy.runSeeder('OphCoMessaging', 'CreateMessageSeeder', {}).as('seederData')
    })

    it('ensures that the message is sent successfully', () => {
       
        cy.get('@seederData').then((data) => {

            // login as admin user and navigate to home page
            cy.login()
            cy.visit('/')

            // create a patient and add a messaging event
            cy.createPatient()
                .then((patient) => {
                    return cy.getEventCreationUrl(patient.id, 'OphCoMessaging')
                        .then((url) => {
                            return [url, patient];
                        });
                })
                .then(([url, patient]) => {
                cy.visit(url);
                });

            // send the message to our seeded user
            cy.intercept('/OphCoMessaging/Default/autocompleteMailbox*').as('mailboxAutoComplete')           
            cy.getBySel('fao-search').type(data.user.fullName)
            cy.wait('@mailboxAutoComplete')
            cy.getBySel('autocomplete-match').contains(data.user.fullName).click()
            cy.getBySel('your-message').type(data.user.messageText)
            cy.getBySel('preview-and-check').click()
            cy.getBySel('send-message').click()

            // log in as our seeded user
            cy.login(data.user.username, data.user.password)
            cy.visit('/')

            // assert that the message has been sent successfully
            cy.getBySel('home-mailbox-message-text').contains(data.user.messageText).should('be.visible')

        })

    });

});