describe('test functionality for post-op complications admin screen', () => {
    let seederData;
    
    beforeEach(() => {
        cy.login();
    })

    before(() => {
        cy.login()
            .then(() => {
                return cy.runSeeder('Admin', 'PostOpComplicationSeeder');
            })
            .then((data) => {
                seederData = data;
            });
    })

    it('adds a post-op complication and verifies that it appears when searching for its name', () => {
        cy.visit('/oeadmin/PostOpComplication/edit')
        cy.getBySel('post-op-complication-admin-name').type(seederData.add_complication_name)
        cy.getBySel('post-op-complication-admin-save').click()
        cy.getBySel('post-op-complication-admin-search').type(seederData.add_complication_name)
        cy.getBySel('post-op-complication-admin-search-btn').click()
        cy.contains(seederData.add_complication_name).parent('tr').should('be.visible')
    });

    it('ensures that post-op complications cannot be deleted if they are assigned to a subspecialty', () => {
        cy.visit('/oeadmin/PostOpComplication/list')
        cy.getBySel('post-op-complication-admin-search').type(seederData.assigned_complication_name)
        cy.getBySel('post-op-complication-admin-search-btn').click()
        cy.getBySel('post-op-complication-admin-row').contains(seederData.assigned_complication_name).parent('tr').find('input[type=checkbox]').should('not.exist')
    });

    it('deletes a post-op complication', () => {
        cy.visit('/oeadmin/PostOpComplication/list')
        cy.getBySel('post-op-complication-admin-search').type(seederData.delete_complication_name)
        cy.getBySel('post-op-complication-admin-search-btn').click()
        cy.getBySel('post-op-complication-admin-row').contains(seederData.delete_complication_name).parent('tr').find('input[type=checkbox]').check()
        cy.getBySel('post-op-complication-admin-delete-btn').click()
        cy.getBySel('post-op-complication-admin-row').contains(seederData.delete_complication_name).should('not.exist')
    });
});