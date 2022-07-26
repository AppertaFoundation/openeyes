describe('the CVI delete UI works as expected', () => {
    beforeEach(() => {
        cy.login()
            .then(() => {
                return cy.createEvent('OphCoCvi');
            })
            .as('event');
    });

    it('the delete confirmation pops when the trash icon is clicked, and is hidden properly when cancelled', function () {
        cy.visit(this.event.urls.view)
            .then(() => {
                cy.get('#js-delete-event').should('not.be.visible');
                cy.get('#js-delete-event-btn').click();
                cy.get('#js-delete-event').should('be.visible');

                cy.get('#et_canceldelete').click();
                cy.get('#js-delete-event').should('not.be.visible');
                // TODO: make a generic assertion on this - have had to check the 
                // css property because the way we currently make it visible fools
                // cypress into still thinking it's not visible.
                cy.get('.event-footer-actions .spinner').invoke('css', 'display')
                    .should('eq', 'none');
            });
    });
});