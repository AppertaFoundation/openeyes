describe('Users profile - Site page functionality', () => {
    beforeEach(function()  {
        cy.login()
            .then(() => {
                cy.runSeeder(null, 'UserProfileSeeder').as('seederData').then(seederData => {
                    cy.login(this.seederData.user.username, this.seederData.user.password).as('loggedInUser');
                    cy.visit('/profile/sites');
                });
            });
    });

    function dropDownOnlyContainsActiveSites(inactive_sites, active_sites) {
        inactive_sites.forEach(site => {
            cy.getBySel('site-dropdown').should('not.contain', site.name);
        });

        active_sites.forEach(site => {
            cy.getBySel('site-dropdown').should('contain', site.name);
        });
    }

    function userCanAddSite(site) {
        cy.getBySel('site-dropdown').select(site.id);
        cy.getBySel('added-sites-table').should('contain', site.name);
    }
    function userCanRemoveSite(site) {
        cy.getBySel('added-sites-table').contains(site.name).within(td => {
            td.closest('tr').find(`input[type=checkbox]`).click();
        });

        cy.getBySel(`event-action-remove-selected-site`).click();
        cy.getBySel('added-sites-table').should('not.contain', site.name);
    }

    it('ensures that the site works correctly', function()  {
        dropDownOnlyContainsActiveSites(this.seederData.inactive_sites, this.seederData.active_sites);
        userCanAddSite(this.seederData.active_sites[0]);
        userCanRemoveSite(this.seederData.active_sites[0]);
    });

    it('ensures user can remove inactive sites', function()  {
        cy.createModels("UserSite",
            [],
            {
                'user_id': this.seederData.user.user_id,
                'site_id': this.seederData.inactive_sites[0].id
            }
        );
        cy.visit('/profile/sites');
        const site_name = this.seederData.inactive_sites[0].name;
        cy.getBySel('added-sites-table').contains(`${site_name} (inactive)`);

        userCanRemoveSite(this.seederData.inactive_sites[0]);
    });
});
