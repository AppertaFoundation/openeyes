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
});
