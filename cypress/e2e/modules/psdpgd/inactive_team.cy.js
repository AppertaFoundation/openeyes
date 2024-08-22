describe('ensures that users in inactive teams cannot create or edit drug administration events', () => {
    let seederData;

    before(() => {
        cy.login();

        cy.runSeeder('OphDrPGDPSD', 'InactiveTeamSeeder').then((data) => {
            seederData = data;
        });
    });

    beforeEach(() => {
        cy.login(seederData.user.username, seederData.user.password);
    });

    it('verifies drug administration events cannot be created by users in inactive teams associated with a PSD', () => {
        cy.visit(seederData.event.urls.view);

        cy.getBySel('add-new-event-button').click().then(() => {
            cy.get('[data-test="new-event-subspecialty"], [data-test="add-new-event-subspecialty"]').first().click().then(() => {
                cy.getBySel('new-event-context').first().click().then(() => {
                    cy.getBySel('add-new-event-OphDrPGDPSD').should('have.class', 'add_event_disabled');
                });
            });
        });
    });

    it('verifies drug administration events cannot be edited by users in inactive teams associated with a PSD', () => {
        cy.visit(seederData.event.urls.view);

        cy.getBySel('button-event-header-tab-edit').should('not.exist');
    });
});
