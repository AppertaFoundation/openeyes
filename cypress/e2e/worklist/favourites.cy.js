describe('Changed site reflects correctly in the worklist filter and favourite popup', () => {
    beforeEach(() => {
        cy.login()
            .then(() => {
                cy.visitWorklist();

                // Close the navbar if it is open
                cy.getBySel('nav-worklist-btn').then(($ele) => {
                    if ($ele.hasClass('open')) {
                        $ele.click();
                    }
                });
            });
    });

    it('Before adding favourites, updated site should reflect in the worklist filter and favourite popup', function () {
        cy.getBySel('change-firm').click();
        cy.getBySel('change-site-site-context-popup').then(($el) => {
            const siteNameSiteContextPopup = $el[0].selectedOptions[0].label
            cy.getBySel('confirm-change-site-context-popup').click();

            cy.getBySel("worklist-filter-panel-select-site").find("option:selected")
                .contains(siteNameSiteContextPopup);

            cy.getBySel('worklist-favourite-btn').click();

            cy.getBySel('worklist-save-filter-site').then(($el2) => {
                const siteNameFavouritePopup = $el2.text()
                //Site in header and favourite popup is same,test should pass
                expect(siteNameSiteContextPopup).to.eq(siteNameFavouritePopup);
            });
        });
    });

    it('After adding favourites, updated site should reflect in the worklist filter and favourite popup', () => {
        cy.getBySel('worklist-favourite-btn').click();
        
        //Add first favourite by clicking star in the header and save
        cy.getBySel('input-favourite-name').type('Favourite 01');
        cy.getBySel('save-favourite-filter').click();

        //Change the site from header to check if its equal to favourite pop up and worklist filter
        cy.getBySel('change-firm').click();

        // Select a different site option in the dropdown and save
        cy.getBySel('change-site-site-context-popup').find('option').then(($option) => {
            let notSelectedOptionValues = $option.map(function(index, element) { 
                if (!element.selected) {
                    return element.value; 
                }
            }).get(); 
            cy.getBySel('change-site-site-context-popup').select(notSelectedOptionValues[0]);
        });
        cy.getBySel('confirm-change-site-context-popup').click();

        //Check the site in the header
        cy.getBySel('user-profile-site-institution', "", { timeout: 5000 }).then(($el) => {
            let siteName = $el.text().trim().split(" ")[0];

            //Click on worklist filter
            cy.getBySel('nav-worklist-btn').click();

            // Site does not update correctly in the worklist filter and favourite popup
            cy.getBySel("worklist-filter-panel-select-site")
                .find("option:selected")
                .contains(siteName);

            // Click on favourite in header and check the site
            cy.getBySel('worklist-favourite-btn').click();
            cy.getBySel('worklist-save-filter-site').then(($el2) => {
                const siteNameFavouritePopup = $el2.text()
                //Site in header and favourite popup is same, test should pass
                expect(siteName).to.eq(siteNameFavouritePopup);
            });
        });
    })
});

describe('Worklist favorites with the same name should not be allowed', () => {
    before(() => {
        cy.login()
        .then(() => {
            cy.visitWorklist();

            // Close the navbar if it is open
            cy.getBySel('nav-worklist-btn').then(($ele) => {
                if ($ele.hasClass('open')) {
                    $ele.click();
                }
            });

            //Delete all the favourites from the starred list
            cy.getBySel('nav-worklist-btn').click();
            cy.getBySel('worklist-mode-starred').click();

            cy.getBySel('favourite').each(($el) => {
                cy.wait(2000);
                cy.getBySel('favourite').first().should('be.visible').find('[data-test="remove-favourite"]').click();
            });            

            cy.intercept('POST', '/worklist/storeFilter').as('storeFilter');
        });
    });

    it('Adding favourite panel updates existing favourite when names match', function () {
        cy.getBySel('worklist-favourite-btn').click();
        cy.getBySel('input-favourite-name').type('Favourite 01');
        cy.getBySel('save-favourite-filter').click();

        cy.wait('@storeFilter');

        // there should only be one
        cy.getBySel('favourite-details').should('have.length', 1);

        // ensure favourite contains correct context
        cy.getBySel('worklist-filter-panel-select-context').find('option:selected').invoke('text').as('selectedOptionText');
        cy.get('@selectedOptionText')
            .then((selectedOptionText) => {
                cy.getBySel('favourite-details')
                .first()
                .should('contain.text', selectedOptionText.trim()); 
            });        

        // change context
        cy.getBySel('worklist-filter-panel-select-context').find('option').then(($option) => {
            let notSelectedOptions = $option.map(function(index, element) { 
                if (!element.selected) {
                    return element; 
                }
            }).get(); 
            cy.getBySel('worklist-filter-panel-select-context').select(notSelectedOptions[0].value);
        });

        // Attempt to add a second favourite
        cy.getBySel('worklist-favourite-btn').click();
        cy.getBySel('input-favourite-name').clear({force:true});
        cy.getBySel('input-favourite-name').type('Favourite 01');
        cy.getBySel('save-favourite-filter').click();

        cy.wait('@storeFilter');

        // there should still just be one
        cy.getBySel('favourite-details').should('have.length', 1);

        // favourite text should contain the new context
        cy.getBySel('worklist-filter-panel-select-context').find('option:selected').invoke('text').as('selectedOptionText');
        cy.get('@selectedOptionText')
            .then((selectedOptionText) => {
                console.log(['sel opt text', selectedOptionText.trim()]);
                cy.getBySel('favourite-details')
                .first()
                .should('contain.text', selectedOptionText.trim()); 
            });  

        // Delete all the favourites from the starred list
        cy.getBySel('worklist-mode-starred').click();
        cy.getBySel('favourite').each(($el) => {
            cy.wait(2000);
            cy.getBySel('favourite').first().should('be.visible').find('[data-test="remove-favourite"]').click();
        });
        
        //All favourites should be deleted to pass the test
        cy.getBySel('favourite').should('not.exist');
    });
});