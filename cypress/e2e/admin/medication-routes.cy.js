describe('behaviour of the admin screen for medication routes', () => {

    beforeEach(() => {
        cy.login();
    });

    it('deactivates medication routes', function () {
        cy.visit('OphDrPrescription/routesAdmin/list');
        cy.intercept('/OphDrPrescription/routesAdmin/deactivate').as('deactivate');

        cy.get('input[name="delete"]').as("deactivateButton");
        cy.get('tr[class="clickable"] td[data-test="getIsActiveIcon"] i.tick')
            .first()
            .closest('tr')
            .find('input[type="checkbox"]')
            .as("checkbox", { static: true })
            .click();

        cy.get("@checkbox").invoke('attr', 'value').then(routeId => {
            cy.get("@deactivateButton").click();
            cy.wait("@deactivate");

            cy.visit('OphDrPrescription/routesAdmin/list');

            cy.get(`input[type="checkbox"][value=${routeId}]`).closest('tr').within(tr => {
                cy.getBySel('getIsActiveIcon', ' i').should('have.class', 'remove');
            });
        });
    });
});
