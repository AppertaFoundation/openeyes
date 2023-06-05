describe('setting field type views and behaviours for system settings', () => {
    before(() => {
        cy.login()
            .then(() => {
                return cy.runSeeder(null, 'CreateAdminSettingsSeeder');
            }).as('seederData');
    });

    it('displays and permits toggling of a checkbox setting', () => {
        cy.get('@seederData').then((data) => {
            const setting = data.checkbox;
            // The regular expression below, which is passed to `contains`, is used to enforce an exact match of the setting name.
            // Without it the name may be matched inside a longer string, e.g. 'aut' in 'automatic'
            // Unfortunately JS does not currently have a standard for escaping fragments used in the construction of regular expressions,
            // so this operates under the assumption that the setting name will not contain special characters.
            const exactName = new RegExp(`^${setting.name}$`);

            cy.visit('/admin/settings').then(() => {
                cy.getBySel('admin-system-setting')
                    .contains(exactName)
                    .siblings('[data-test="admin-system-setting-value"]')
                    .invoke('text')
                    .should('equal', setting.startedValue);

                cy.getBySel('admin-system-setting').contains(exactName).click().then(() => {
                    cy.location('search')
                        .then((s) => new URLSearchParams(s))
                        .invoke('get', 'key')
                        .should('equal', setting.key);

                    if (setting.startedChecked) {
                        cy.getBySel('setting-checkbox').should('be.checked');
                        cy.getBySel('setting-checkbox').uncheck();
                    } else {
                        cy.getBySel('setting-checkbox').should('not.be.checked');
                        cy.getBySel('setting-checkbox').check();
                    }

                    cy.getBySel('save-system-setting').click().then(() => {
                        cy.location('pathname').should('equal', '/admin/settings');

                        cy.getBySel('admin-system-setting')
                            .contains(exactName)
                            .siblings('[data-test="admin-system-setting-value"]')
                            .invoke('text')
                            .should('equal', setting.toggledValue);
                    })
                });
            })
        })
    });
});
