describe('verify behaviour for editing of messages', () => {
    beforeEach(() => {
        cy.login().then(() => {
            return cy.runSeeder('OphCoMessaging', 'EditMessageSeeder')
        }).as('seederData');
    });

    it('ensures that a message being edited can be saved without modification', () => {
        cy.get('@seederData').then((data) => {
            cy.login();

            cy.visit(data.messageEvent.urls.edit);

            // The user should be redirected to the event view upon saving the event
            cy.getBySel('event-action-save').first().click().then(() => {
                cy.location('pathname').should('equalIgnoreCase', data.messageEvent.urls.view);

                cy.getBySel('message-primary-recipient-mailbox-name').should('contain', data.primaryMailboxName);
                cy.getBySel('message-cc-recipient-mailbox-names').should('contain', data.ccMailboxName);
            });
        });
    });

    it('preserves the recipient data in the form', () => {
        cy.get('@seederData').then((data) => {
            cy.login();

            cy.visit(data.messageEvent.urls.edit);

            cy.getBySel('message-recipient-id').should('have.length', data.messageEventRecipientCount);
        });
    });

    it('permits changing the urgent status of a message', () => {
        cy.get('@seederData').then((data) => {
            cy.login();

            cy.visit(data.messageEvent.urls.edit);

            cy.getBySel('message-urgent-status').then((urgentElement) => {
                const wasUrgent = urgentElement.attr('checked') === 'checked';

                cy.getBySel('message-urgent-status').click().then(() => {
                    cy.getBySel('event-action-save').first().click().then(() => {
                        cy.location('pathname').should('equalIgnoreCase', data.messageEvent.urls.view);

                        cy.getBySel('message-urgent-indicator').should('have.length', wasUrgent ? 0 : 1);
                    });
                });
            });
        })
    });
});
