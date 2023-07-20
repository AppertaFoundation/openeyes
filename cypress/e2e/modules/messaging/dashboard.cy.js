describe('validates the interactive behaviour of the mailbox dashboard view', () => {

    let seederData;

    before(function () {
        // Seed:
        // - user (with atttributes username, password and fullName)
        // - mailbox detail to find the right link in the sidebar
        // - list of messages
        // - folder list that should be shown for the mailbox
        cy.login()
            .then(() => {
                return cy.runSeeder('OphCoMessaging', 'DashboardSeeder');
            })
            .then(function(data) {
                seederData = data;
            });
    });

    function mapFolderIdToSelector(folderId)
    {
        const remappedId = {
            'unread_all': 'unread-all',
            'read_all': 'read-all',
            'read_cc': 'read-copied',
            'unread_all': 'unread-all',
            'unread_cc': 'unread-copied'

        }[folderId] ?? folderId;

        return `home-mailbox-${remappedId}`;
    }

    it('updates folder list counts based on the response of the endpoint for marking it read', () => {
        cy.login(seederData.user.username, seederData.user.password);
        cy.visit('/');
        cy.getBySel('home-mailbox-name').click();

        seederData.messages.forEach((messageEvent) => {
            const expectedCount = 1 + Math.floor(Math.random() * 5);

            // set up fake response containing count for folders we're testing
            let folderCounts = {};
            seederData.folders.forEach((folderId) => {
                folderCounts[folderId] = expectedCount;
            })
            const response = {
                body: {
                    mailboxes_with_counts: {
                    }
                }
            };
            response.body.mailboxes_with_counts[seederData.userMailbox.id] = folderCounts;

            cy.intercept(
                `/OphCoMessaging/Default/markRead/*`,
                response
            ).as('markAsReadAction');

            // click mark as read, then assert folders are updated to counts
            cy.get(`[data-event-id*=${messageEvent.id}]`).should('be.visible')
                .within(() => {
                    cy.getBySel('mark-as-read-btn').click();
                });
            cy.wait('@markAsReadAction')
                .then(() => {
                    seederData.folders.forEach((folderId) => {
                        cy.getBySel(mapFolderIdToSelector(folderId))
                            .within(() => {
                                cy.get('span').should('include.text', expectedCount);
                            });
                    });
                });
        });
    });
});