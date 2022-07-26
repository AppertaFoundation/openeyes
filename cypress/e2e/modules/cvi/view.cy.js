describe('CVI events are viewable', () => {
    beforeEach(() => {
        cy.login()
            .then(() => {
                return cy.createEvent('OphCoCvi');
            })
            .as('event');
    });

    it('displays the event', function () {
        cy.visit(this.event.urls.view)
            .then(() => {
                cy.get('.event-title').contains('CVI');
            });
    });
})