describe('check visibility  of send to and copy to', () => {

    beforeEach(() => {
        cy.login().then(() => {
            return cy.runSeeder('OphCoMessaging', 'CreateMessageSeeder');
        }).as('seederData');
    });

    it('send to set to current user', () => {

        cy.get('@seederData').then((data) => {

            // login as admin user and navigate to home page
            cy.login();
            cy.visit('/');

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

            // add send to
            cy.getBySel('fao-search').type("Mr Admin Admin");
            cy.getBySel('autocomplete-match').contains("Mr Admin Admin").click();

            // assert that send to set to current user
            cy.getBySel('fao-field').within(() => {
                cy.get('ul').first().within(() => {
                    cy.get('li').first().contains("Mr Admin Admin").should('be.visible');
                });
            });

            // assert that copy to is not visible
            cy.getBySel('copyto-search').should(('not.be.visible'));

            // remove user from send to
            cy.getBySel('fao-field').within(() => {
                cy.get('ul').first().within(() => {
                    cy.get('li').first().within(() => {
                        cy.get('i').click();
                    });
                });
            });

            // assert that copy to is visible
            cy.getBySel('copyto-search').should(('be.visible'));
        });
    });

    it('send to set to not current user', () => {

        cy.get('@seederData').then((data) => {

            // login as admin user and navigate to home page
            cy.login();
            cy.visit('/');

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

            // add send to
            cy.getBySel('fao-search').type(data.user.fullName);
            cy.getBySel('autocomplete-match').contains(data.user.fullName).click();

            // assert that send to is set
            cy.getBySel('fao-field').within(() => {
                cy.get('ul').first().within(() => {
                    cy.get('li').first().contains(data.user.fullName).should('be.visible');
                });
            });

            // assert that copy to is visible
            cy.getBySel('copyto-search').should(('be.visible'));

            // add send to same as Send to
            cy.getBySel('copyto-search').type(data.user.fullName);
            cy.getBySel('autocomplete-match',':visible').contains(data.user.fullName).click();

            // assert that copy to is visible
            cy.getBySel('copyto-search').should(('be.visible'));

            // add copy to of a different user
            cy.getBySel('copyto-search').type("Mr Theatre Diary Admin");
            cy.getBySel('autocomplete-match',':visible').contains("Mr Theatre Diary Admin").click();

            // assert that send to has been added
            cy.getBySel('copyto-field').within(() => {
                cy.get('ul').first().within(() => {
                    cy.get('li').first().contains("Mr Theatre Diary Admin").should('be.visible');
                });
            });

            // remove user from copy to
            cy.getBySel('copyto-field').within(() => {
                cy.get('ul').first().within(() => {
                    cy.get('li').first().within(() => {
                        cy.get('i').click();
                    });
                });
            });

            // assert that send to is still set
            cy.getBySel('fao-field').within(() => {
                cy.get('ul').first().within(() => {
                    cy.get('li').first().contains(data.user.fullName).should('be.visible');
                });
            });
        });
    });

});