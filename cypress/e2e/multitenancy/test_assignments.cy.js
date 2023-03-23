describe('ensure multi tenanted settings behave according to their assignments', () => {
    let seederData;
    
    before(function () {
        /**
         * Call the seeder that provides the test data
         * 
         * Data included is as follows:
         *  - user_logins:
         *      - restricted_to_institution_user
         *          This is used for testing assignments against the current institution
         *      - installation_user
         *          This is used as a control user, they should not be able to see any assignments other than installation level
         *  - event_resource
         *      This is provided as a method of testing for visibility of data in an examination event
         *  - values:
         *      This contains a structured array of premade MT assignments to test for 
         *      Items in the array is structured as follows:
         *          - admin
         *              Contains data for testing functionality in the item's respecive admin screen:
         *                  - test_url
         *                      A URL to test that the assignment is visible
         *                  - control_url
         *                      A URL to test that the assignment is not visible
         *                  - data_test
         *                      The data-test used to test the existence/availability of the value in the front end
         *                  - element_type
         *                      (Optional) If provided will change the method of locating the test value based on the type of DOM element that it's contained in
         *                  - value
         *                      The value to test for
         *          - event
         *              Contains data for testing availability of the assigned value in the context of an event
         *                  - data_test
         *                      The data-test used to test the existence/availability of the value in the front end
         *                  - element_type
         *                      (Optional) If provided will change the method of locating the test value based on the type of DOM element that it's contained in
         *                  - value
         *                      The value to test for
        */
        cy.runSeeder('Admin', 'MultiTenantedDataSeeder')
            .then(function(data) {
                seederData = data;
            });
    });

    it('allows a user to see reference data in the admin screens that they have permission to see, based on their institution context and administrative rights', function () {
        let login = seederData.user_logins.restricted_to_institution_user;
        cy.login(login.username, login.password, login.site_id, login.institution_id);

        seederData.values.forEach((val) => {
            cy.visit(val.admin.test_url ?? val.admin.url);
            cy.assertElementValue(val.admin.data_test, val.admin.value, val.admin.element_type);
            if (val.admin.installation_value !== undefined) {
                cy.assertElementValue(val.admin.data_test, val.admin.installation_value, val.admin.element_type);
            }
        });
    });

    it('does not allow a user to see reference data in the admin screens that they do not have permission to see, based on their institution context and administrative rights', function () {
        let login = seederData.user_logins.installation_user;
        cy.login(login.username, login.password, login.site_id, login.institution_id);

        seederData.values.forEach((val) => {
            cy.visit(val.admin.control_url ?? val.admin.url);
            cy.assertNoElementValue(val.admin.data_test, val.admin.value, val.admin.element_type);
            if (val.admin.installation_value !== undefined) {
                cy.assertElementValue(val.admin.data_test, val.admin.installation_value, val.admin.element_type);
            }
        });
    });

    it('allows a user to see reference data in event screens that they have permission to see, based on their institution context and administrative rights', function () {
        let login = seederData.user_logins.restricted_to_institution_user;
        cy.login(login.username, login.password, login.site_id, login.institution_id);
        cy.visit(seederData.event_resource.edit_url);

        seederData.values.forEach((val) => {
            if (val.event !== undefined) {
                cy.assertOptionAvailable(val.event.data_test, val.event.value, val.event.element_type);
                if (val.event.installation_value !== undefined) {
                    cy.assertOptionAvailable(val.event.data_test, val.event.installation_value, val.event.element_type);
                }
            }
        });
    });

    it('does not allow a user to see reference data in event screens that they do not have permission to see, based on their institution context and administrative rights', function () {
        let login = seederData.user_logins.installation_user;
        cy.login(login.username, login.password, login.site_id, login.institution_id);
        cy.visit(seederData.event_resource.edit_url);

        seederData.values.forEach((val) => {
            console.log(val)
            if (val.event !== undefined) {
                cy.assertOptionNotAvailable(val.event.data_test, val.event.value, val.event.element_type);
                if (val.event.installation_value !== undefined) {
                    cy.assertOptionAvailable(val.event.data_test, val.event.installation_value, val.event.element_type);
                }
            }
        });
    });
});