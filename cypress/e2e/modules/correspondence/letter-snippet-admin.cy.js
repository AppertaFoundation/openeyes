describe('Appropriate checkboxes should get checked for delivery methods', () => {
    beforeEach(() => {
        cy.login();
    });

    it('pressing search does not cause a crash', () => {
        cy.visit('/OphCoCorrespondence/oeadmin/snippet/list');
        cy.getBySel('search-button').click();
        cy.get('#generic-admin-list').find('table').should('be.visible');
    })
});