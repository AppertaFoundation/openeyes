describe('verifies the desired behaviour of shared mailboxes for messaging', () => {

    const REPLY_REQUIRED_MSG_SUB_TYPE = 'ReR';
    const REPLY_NOT_REQUIRED_MSG_SUB_TYPE = 'RNR';

    let seederData;

    before(function () {
        // Seed:
        // - 2 users - return user1 and user2 (with atttributes username, password and fullName)
        // - a test team - return teamName (assign admin and user1 to the team via seeder)
        // - a user shared mailbox - return userMailbox (with attributes name and messageText) (assign user1 and user2 to it via seeder)
        // - a team shared mailbox - return teamMailbox (with attributes name and messageText) (assign test team to it via seeder)
        // - 2 message events - 1 with sender admin and recipient userMailbox (and reply required); 1 with recipient teamMailbox (and reply not required) - return messageEvent1 and messageEvent2
        cy.login()
            .then(() => {
                return cy.runSeeder('OphCoMessaging', 'TestMailboxSeeder');
            })
            .then(function(data) {
                seederData = data;
            });
    });

    it('ensures that shared mailboxes exhibit the required behaviour from the home page', function () {     

        let data = seederData;

        // login as admin user and navigate to home page
        cy.login()
        cy.visit('/')

        // assert that the test user shared mailbox is not visible on the home page (of admin user)
        cy.getBySel('home-mailbox-name').contains(data.userMailbox.name).should('not.exist')

        // assert that the test team shared mailbox is visible on the home page (of admin user)
        cy.getBySel('home-mailbox-name').contains(data.teamMailbox.name).should('be.visible')

        // log in as user1
        cy.login(data.user1.username, data.user1.password)
        cy.visit('/')

        // assert that the first seeded message has been sent successfully to the user shared mailbox
        cy.getBySel('home-mailbox-name').contains(data.userMailbox.name)
            .parent('div')
            .within(() => {
                cy.getBySel('home-mailbox-all').click({force: true})
            })
        cy.getBySel('home-mailbox-message-reply-required').should('be.visible')
        cy.getBySel('home-mailbox-message-sub-type').contains(REPLY_REQUIRED_MSG_SUB_TYPE).should('be.visible')
        cy.getBySel('home-mailbox-message-text').contains(data.userMailbox.messageText).should('be.visible')

        // assert that the second seeded message has been sent successfully to the team shared mailbox
        cy.getBySel('home-mailbox-name').contains(data.teamMailbox.name)
            .parent('div')
            .within(() => {
                cy.getBySel('home-mailbox-all').click({force: true})
            })
        cy.getBySel('home-mailbox-message-reply-required').should('not.exist')
        cy.getBySel('home-mailbox-message-sub-type').contains(REPLY_NOT_REQUIRED_MSG_SUB_TYPE).should('be.visible')
        cy.getBySel('home-mailbox-message-text').contains(data.teamMailbox.messageText).should('be.visible')

    });

    it('ensures that the read/unread functionality of a shared mailbox message behaves as expected (and that said message actions are audited correctly)', function () {

        const READ_STATUS = 'Read by: ';
        const UNREAD_STATUS = 'Unread';
        const AUDIT_EVENT_TYPE = 'Message';
        const AUDIT_ACTION_READ = 'marked read';
        const AUDIT_ACTION_UNREAD = 'marked unread';

        let data = seederData;

        // log in as user1
        cy.login(data.user1.username, data.user1.password)

        // mark the message sent to the user shared mailbox as read
        cy.visit(data.messageEvent1.urls.view)
        cy.getBySel('mark-as-read').click()

        // log in as user2
        cy.login(data.user2.username, data.user2.password)

        // assert that the user shared mailbox message is marked as read (by user1)
        cy.visit(data.messageEvent1.urls.view)
        cy.getBySel('read-status').contains(READ_STATUS + data.user1.fullName).should('be.visible')

        // mark the message as unread
        cy.getBySel('mark-as-unread').click()

        // log back in as user1
        cy.login(data.user1.username, data.user1.password)

        // assert that the user shared mailbox message is marked as unread
        cy.visit(data.messageEvent1.urls.view)
        cy.getBySel('read-status').contains(UNREAD_STATUS).should('be.visible')

        // log in as admin
        cy.login()

        // visit the Audit page and interrogate the message events ...
        cy.visit('/audit')
        cy.getBySel('audit-event-type').select(AUDIT_EVENT_TYPE)

        // assert that the user shared mailbox message has been audited as 'marked read' by user1
        cy.getBySel('audit-action').select(AUDIT_ACTION_READ)
        cy.fillAndSelectAutocomplete(data.user1.fullName);
        cy.getBySel('create-audit-button').scrollIntoView().click()

        cy.getBySel('audit-anchor')
            .should('have.attr', 'href')
            .and('include', data.messageEvent1.urls.view.substr(29))

        // assert that the user shared mailbox message has been subsequently audited as 'marked unread' by user2
        cy.getBySel('audit-action').select(AUDIT_ACTION_UNREAD)
        cy.fillAndSelectAutocomplete(data.user2.fullName)
        cy.getBySel('create-audit-button').click()

        cy.getBySel('audit-anchor')
            .should('have.attr', 'href')
            .and('include', data.messageEvent1.urls.view.substr(29))

    });

    it('ensures that the reply/delete functionality of a shared mailbox message behaves as expected (and that said message actions are audited correctly)', function () {

        const REPLY_TEXT = 'Reply to Mr Admin Admin (testing reply functionality of shared mailbox messages)';
        const DELETE_TEXT = 'Testing delete functionality of shared mailbox messages';
        const AUDIT_EVENT_TYPE = 'Message';
        const AUDIT_ACTION_REPLY = 'Reply added';
        const AUDIT_ACTION_DELETE = 'delete-request';

        let data = seederData;

        // log in as user2
        cy.login(data.user2.username, data.user2.password)

        // view the user shared mailbox message
        cy.visit(data.messageEvent1.urls.view)

        // assert that the message type is ReR - reply required - and that it is possible to reply
        cy.getBySel('message-type').contains(REPLY_REQUIRED_MSG_SUB_TYPE).should('be.visible')
        cy.getBySel('your-reply').should('be.visible')

        // reply to the sender of the user shared mailbox message (admin)
        cy.getBySel('your-reply').type(REPLY_TEXT)
        cy.getBySel('preview-and-check').click()
        cy.getBySel('send-reply').click()

        // log in as the sender (admin)
        cy.login()
        cy.visit('/')

        // assert that the reply is visible and identifies both the user (user2) and mailbox (user shared mailbox) that sent it
        cy.getBySel('home-mailbox-message-text').contains(REPLY_TEXT).should('be.visible')

        // log in as user1
        cy.login(data.user1.username, data.user1.password)
        cy.visit('/')

        // assert that the reply is not visible to user1 (it should only be visible to the user that created the original message - admin in this case)
        cy.getBySel('home-mailbox-message-text').contains(REPLY_TEXT).should('not.exist')

        // request the deletion of the user shared mailbox message
        cy.visit(data.messageEvent1.urls.view)
        cy.getBySel('event-action-').click()
        cy.getBySel('reason-for-deletion').type(DELETE_TEXT)
        cy.getBySel('delete-event').click()

        // log back in as admin
        cy.login()

        // view the team shared mailbox message
        cy.visit(data.messageEvent2.urls.view)

        // assert that the message type is RNR - reply not required - and that it is not possible to reply
        cy.getBySel('message-type').contains(REPLY_NOT_REQUIRED_MSG_SUB_TYPE).should('be.visible')
        cy.getBySel('your-reply').should('not.exist')

        // visit the Audit page and interrogate the message events ...
        cy.visit('/audit')
        cy.getBySel('audit-event-type').select(AUDIT_EVENT_TYPE)

        // assert that the user shared mailbox message has been audited as 'Reply added' by user2
        cy.getBySel('audit-action').select(AUDIT_ACTION_REPLY)
        cy.fillAndSelectAutocomplete(data.user2.fullName);
        cy.getBySel('create-audit-button').scrollIntoView().click()

        cy.getBySel('audit-anchor')
            .should('have.attr', 'href')
            .and('include', data.messageEvent1.urls.view.substr(29))

        // assert that the user shared mailbox message has been subsequently audited as 'delete-request' by user1
        cy.getBySel('audit-action').select(AUDIT_ACTION_DELETE)
        cy.fillAndSelectAutocomplete(data.user1.fullName)
        cy.getBySel('create-audit-button').click()

        cy.getBySel('audit-anchor')
            .should('have.attr', 'href')
            .and('include', data.messageEvent1.urls.view.substr(29))

    });

});