describe('behaviour of the admin screen for contact label', () => {

    beforeEach(() => {
        cy.login()
    });

    it('check add contact label has Is Private and Max Number Per Patient ', function () {

        cy.visit(`/admin/addContactlabel`);

        cy.getBySel('is_private').should('exist');

        cy.getBySel('max_number_per_patient').should('exist');

        cy.generateRandomString(6).then((randomString) => {
            cy.getBySel('name').type(randomString);

            cy.getBySel('is_private').click();

            cy.getBySel('max_number_per_patient').type('3');

            cy.getBySel('et_save').click();

            cy.getBySel('admin_contactlabels').find('td').contains(randomString);
        });

    });

});