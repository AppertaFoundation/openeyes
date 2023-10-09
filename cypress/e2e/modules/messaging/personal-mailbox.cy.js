describe('personal mailbox', () => {

    before(() => {
        // Seed a single user - return user (with atttributes username, password, fullName and messageText)
        cy.login()
            .then(() => {
                return cy.runSeeder('OphCoMessaging', 'CreateMessageSeeder');
            }).as('seederData');
    });

    it('change users name', () => {

        cy.get('@seederData').then((data) => {

            // login as admin user and navigate to home page
            cy.login();

            cy.createPatient()
                .then((patient) => {
                    return cy.getEventCreationUrl(patient.id, 'OphCoMessaging')
                        .then((url) => {
                            return [url, patient];
                        });
                })
                .then(([url, patient]) => {
                    cy.visit(url);

                    cy.getBySel('fao-search').type(data.user.fullName);

                    cy.getBySel('autocomplete-match').should('exist');

                    cy.visit(`/admin/editUser/${data.user.id}`);

                    cy.getBySel('first_name_field').clear().type('test');

                    cy.getBySel('last_name_field').clear().type('user');

                    cy.getBySel('et_save').click();

                    cy.visit(url);

                    cy.getBySel('fao-search').type('test user');

                    cy.getBySel('autocomplete-match').should('exist');

                });

        });

    });

});