describe('behaviour of the admin screen for medication routes', () => {


    beforeEach(() => {
        cy.login();
    });

    it('delete medication routes', function () {
        cy.visit('OphDrPrescription/routesAdmin/list');

        // Alias the routeId
        cy.get('tr[class="clickable"]').first().within(() => {
            cy.get('input[type="checkbox"]').as('routeCheckbox');

            cy.get('@routeCheckbox').invoke('val').as('routeId');

            cy.get('@routeCheckbox').click();
        });

        cy.get('input[name="delete"]').click();

        // Use the alias to check if the row with the routeId doesn't exist
        cy.get('@routeId').then((routeId) => {
            cy.get(`tr[data-id="${routeId}"]`).should('not.exist');
        });
    });


});
