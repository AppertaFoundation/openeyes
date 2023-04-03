describe('add a shared mailbox via the Admin:Shared Mailbox page', () => {

    before(() => {

        // login as admin user and navigate to home page
        cy.login()
        cy.visit('/')

        // Seed:
        // - mailboxNameToCreate (unique/faked)
        // - 2 x users - return userNames
        // - 2 x teams - return teamNames
        cy.runSeeder('Admin', 'AddMailboxSeeder', {}).as('seederData')

    })

    it('ensures that a shared mailbox is successfully created', () => {

        cy.get('@seederData').then((data) => {

            // navigate to the Admin:Shared Mailboxes page
            cy.visit('/OphCoMessaging/SharedMailboxSettings/create')

            // enter the shared mailbox name
            cy.getBySel('mailbox-name').type(data.mailboxNameToCreate)

            cy.intercept('/user/autocomplete*').as('userSearchAutocomplete')

            // assign user(s) to the shared mailbox
            data.userNames.forEach(userName => {
                cy.getBySel('mailbox-user-search').type(userName)
                cy.wait('@userSearchAutocomplete')
                cy.getBySel('autocomplete-match').contains(userName).click()
            })

            cy.intercept('/oeadmin/team/autocomplete*').as('teamSearchAutocomplete')

            // assign team(s) to the shared mailbox
            data.teamNames.forEach(teamName => {
                cy.getBySel('mailbox-team-search').type(teamName)
                cy.wait('@teamSearchAutocomplete')
                cy.getBySel('autocomplete-match').contains(teamName).click()
            })

            // save the shared mailbox
            cy.getBySel('mailbox-save-button').click()

            // assert that the shared mailbox has been successfully created
            cy.getBySel('list-shared-mailbox-name').contains(data.mailboxNameToCreate)
                .scrollIntoView().should('be.visible')

        })

    });

});