describe('Test the recent worklist filters should apply correctly', () => {
    before(() => {
        cy.login()
            .then(() => {
                cy.runSeeder(
                    'Admin', 
                    'WorklistDefinitionSeeder'
                  )
                    .then((body) => {
                        return body;
                    })
                    .as('seederOutput');
                cy.visitWorklist();
            });
    });
    
    it('Test that worklist is filtered correctly when switching between recent filters', () => {
        cy.get('@seederOutput')
            .then((data) => {
                cy.openWorklistNavBar();

                // Loop through all the sites and toggle combined list checkbox
                // depending on the is_combined value
                data.worklists_for_sites.site.forEach((site) => {
                    cy.getBySel('worklist-filter-panel-select-site').scrollIntoView().select(site.short_name);
                    if (site.is_combined) {
                        cy.getBySel('combine-lists-option').scrollIntoView().check();
                    } else {
                        cy.getBySel('combine-lists-option').scrollIntoView().uncheck();
                    }
                    //In order to set up the content of the recents list, 
                    //click on the button 'Show patient pathways' in the worklist filter panel
                    cy.getBySel('show-patient-pathways').scrollIntoView().click();
                });
                
                //Go to Recents filter, switch between filters
                cy.getBySel('worklist-mode-recent-tab').scrollIntoView().click();
                cy.getBySel('worklist-mode-panel-recent-list')
                    .find('[data-test="worklist-filter-panel-template-recent-filter-fav-site"]')
                    .contains(data.worklists_for_sites.site[0].short_name).scrollIntoView().click();
                cy.getBySel('worklist-combined')
                    .should('exist')
                    .find('header h3')
                    .contains('Combined worklists');
        
                cy.getBySel('worklist-mode-recent-tab').scrollIntoView().click();
                cy.getBySel('worklist-mode-panel-recent-list')
                    .find('[data-test="worklist-filter-panel-template-recent-filter-fav-site"]')
                    .contains(data.worklists_for_sites.site[1].short_name).scrollIntoView().click();

                // Finding the site context(for single list) & Asserting that it should exist as non-combined list
                const notCombinedWorklistIds = Object.keys(data.worklists_for_sites.site[1].worklists);
                cy.getBySel(`js-worklist-${notCombinedWorklistIds[0]}`).scrollIntoView().should('exist');
                cy.getBySel(`js-worklist-${notCombinedWorklistIds[1]}`).scrollIntoView().should('exist');

                // Asserting that worklists that are not part of the currently 
                // selected site should not exist in DOM
                const combinedWorklistIds = Object.keys(data.worklists_for_sites.site[0].worklists);
                cy.getBySel(`js-worklist-${combinedWorklistIds[0]}`).should('not.exist');
            });
    })     
})