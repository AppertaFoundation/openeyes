describe('Test text below search bar shows popup with examples that can be used for searching', () => {
    beforeEach(() => {
        cy.resetSystemSettingValue('dob_mandatory_in_search');
        cy.login();
    });

    it('On homepage, when user clicks "See all options" text below searchbar, popup with non-mandatory DOB examples appear', () => {
        cy.setSystemSettingValue('dob_mandatory_in_search', 'off')
            .then(() => {
                cy.visit('/');
            })
            .then(() => {
                cy.get('div.oe-search-patient').find('a[href="#search-help"]').click();
            })
            .then(() => {
                cy.get('div.js-search-popup .oe-popup').find('.title').should('have.text', "Available search patterns");

                cy.get('div.js-search-popup .oe-popup').find('tbody tr:first td').should('have.text', "David Smith");

                cy.get('div.js-search-popup .oe-popup').find('.remove-i-btn').click();
                cy.get('div.js-search-popup').should('not.be.visible');
            });
    });

    it('On homepage, when user clicks "See all options" text below searchbar, popup with mandatory DOB examples appear', () => {
        cy.setSystemSettingValue('dob_mandatory_in_search', 'on')
            .then(() => {
                cy.visit('/');
            })
            .then(() => {
                cy.get('div.oe-search-patient').find('a[href="#search-help"]').click();
            })
            .then(() => {
                cy.get('div.js-search-popup .oe-popup').find('.title').should('have.text', "Available search patterns");

                cy.get('div.js-search-popup .oe-popup').find('tbody tr:first td').should('have.text', "David Smith 31/12/1975");

                cy.get('div.js-search-popup .oe-popup').find('.remove-i-btn').click();
                cy.get('div.js-search-popup').should('not.be.visible');
            });
    });
});
