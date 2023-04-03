describe('add a team via the Admin:Teams page', () => {

    before(() => {
        // Seed:
        // - teamName (faked)
        // - email (faked)
        // - active (true)
        // - 2 user assignments - return userAssignments (with atttributes fullName and role)
        // - 2 team assignments - return teamAssignments
        cy.runSeeder('Admin', 'AddTeamSeeder', {}).as('seederData')
    })

    it('ensures that a team is successfully created', () => {

        cy.get('@seederData').then((data) => {

            // login as admin user and navigate to home page
            cy.login()
            cy.visit('/')

            // navigate to the Admin:Teams page
            cy.visit('/oeadmin/team/add')

            // enter the team name
            cy.getBySel('team-name').type(data.teamName)

            // enter an email if required
            if (data.email) {
                cy.getBySel('team-email').type(data.email)
            }

            // set the active status of the team
            if (data.active) {
                cy.getBySel('team-active').check()
            } else {
                cy.getBySel('team-active').uncheck()
            }

            // assign user(s) to the team and set their team role(s)
            data.userAssignments.forEach(user => {
                cy.getBySel('add-user').click()
                cy.selectAdderDialogOptionText(user.fullName)
                cy.confirmAdderDialog()
                cy.getBySel('team-user-task').last().select(user.role)
            })

            // assign team(s) to the team
            data.teamAssignments.forEach(teamName => {
                cy.getBySel('add-team').click()
                cy.selectAdderDialogOptionText(teamName)
                cy.confirmAdderDialog()
            })

            // save the team
            cy.getBySel('save-button').click()

            // assert that the team has been successfully created
            cy.getBySel('list-team-name').contains(data.teamName).scrollIntoView().should('be.visible')

        })

    });

});