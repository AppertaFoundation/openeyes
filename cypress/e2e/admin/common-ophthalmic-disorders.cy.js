describe('behaviour of the admin screen for common ophthalmic disorders', function () {

    beforeEach(() => {
        cy.login();
        cy.visit("/admin/editcommonophthalmicdisorder");
    });

    function testTitleAfterDropdownChange(dropdown_id) {
        cy.getBySel('admin-title').contains("Common Ophthalmic Disorders");
        cy.get(`#${dropdown_id}`).select(1);
        cy.url().should('include', 'institution_id=');
        cy.url().should('include', 'subspecialty_id=');
        cy.getBySel('admin-title').contains("Common Ophthalmic Disorders");
    }

    it('displays the admin: title after changing the institution', function() {
        testTitleAfterDropdownChange('institution_id');
    });

    it('displays the admin: title after changing the subspecialty', function() {
        testTitleAfterDropdownChange('subspecialty_id');
    });

    it('able to create and set institution correctly', function () {
        cy.get('#add_new').click();

        cy.intercept('/disorder/autocomplete*').as('autocomplete');
        cy.getBySel('new-diagnosis-input').type('Total and subtotal congenital cataract');
        cy.wait('@autocomplete');
        cy.getBySel('autocomplete-match').first().click();

        cy.get('#et_admin-save').click();

        cy.get('.js-disorder-entries').find('.diagnosis-name').contains('Total and subtotal congenital cataract').closest('tr').within(() => {
            cy.getBySel('institution_dropdown').select('The Monachs Trust');

        })

        cy.get('#et_admin-save').click();

        cy.get(`#institution_id`).select('The Monachs Trust');
        cy.get('.js-disorder-entries').find('.diagnosis-name')
            .contains('Total and subtotal congenital cataract')
            .closest('tr').find('[data-test="delete-button"]').click();


        cy.get('#et_admin-save').click();
    });
});
