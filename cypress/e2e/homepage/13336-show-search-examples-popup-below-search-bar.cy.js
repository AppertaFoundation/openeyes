describe('Test text below search bar shows popup with examples that can be used for searching', () => {

    const DOB_MANDATORY_IN_SEARCH_SETTING = 'dob_mandatory_in_search'

    before(() => {

        // reset 'DOB mandatory in search' setting and login as admin user
        cy.resetSystemSettingValue(DOB_MANDATORY_IN_SEARCH_SETTING)
        cy.login()
            .then(() => {
                // Seed:
                // - primaryPattern (object consisting of primaryIdentifierPrompt key and primaryPattern)
                // - secondaryPattern (object consisting of secondaryIdentifierPrompt key and secondaryPattern)
                return cy.runSeeder('', 'SearchExamplesPopupSeeder')
            }).as('seederData')

    })

    it('On homepage, when user clicks "See all options" text below searchbar, popup with non-mandatory DOB examples appear', () => {      
        
        cy.get('@seederData').then((data) => {

            // set 'DOB mandatory in search' to off and visit home
            cy.setSystemSettingValue(DOB_MANDATORY_IN_SEARCH_SETTING, 'off')
            cy.visit('/')
        
            // assert that the search hint under the Search field does not contain the text 'Date of Birth'
            cy.getBySel('home-search-help').should('not.contain', 'Date of Birth')

             // click on the search hint to display the 'Available search patterns' popup window
            cy.getBySel('home-search-help').click()

            // assert that the popup window has been invoked by checking the title text
            cy.getBySel('popup-search-title').should('have.text', "Available search patterns")

            // assert that the example for the first search pattern contains Given Family only
            cy.getBySel('popup-search-example').first().should('have.text', "David Smith")

             // assert that the search example pattern based on the primary identifier prompt is displayed (if applicable)
            Object.keys(data.primaryPattern).forEach (key => {
                cy.contains(key).parent().contains(data.primaryPattern[key]).should('be.visible')
            })

            // assert that the search example pattern based on the secondary identifier prompt is displayed (if applicable) 
            Object.keys(data.secondaryPattern).forEach (key => {
                cy.contains(key).parent().contains(data.secondaryPattern[key]).should('be.visible')
            })

            // click on the 'x' to close the popup window
            cy.getBySel('popup-search-close').click()

            // assert that the popup window is no longer visible
            cy.getBySel('popup-search-window').should('not.be.visible')

        })

    })

    // Please note that this test step has been streamlined to remove duplication that is not dependent on the 'DOB mandatory in search' setting
    it('On homepage, when user clicks "See all options" text below searchbar, popup with mandatory DOB examples appear', () => {

        // set 'DOB mandatory in search' to on, login and and visit home
        cy.setSystemSettingValue(DOB_MANDATORY_IN_SEARCH_SETTING, 'on')
        cy.login()
        cy.visit('/')

        // assert that the search hint under the Search field contains the text 'Date of Birth'
        cy.getBySel('home-search-help').should('contain', 'Date of Birth')

        // click on the search hint to display the 'Available search patterns' popup window
        cy.getBySel('home-search-help').click()

        // assert that the example for the first search pattern contains Given Family + DOB
        cy.getBySel('popup-search-example').first().should('have.text', "David Smith 31/12/1975")
            
    })

})
