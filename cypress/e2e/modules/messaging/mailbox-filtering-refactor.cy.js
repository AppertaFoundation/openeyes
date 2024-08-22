describe('Mailbox and Message Tracking Tests', () => {
  let seederData;

  before(function () {
      //Perform login and run the seeder to get data for testing
      cy.login()
          .then(() => {
              return cy.runSeeder('OphCoMessaging', 'MailboxQueriesSeeder');
          })
          .then(function(data) {
            seederData = data;

          });
  });

  it('check the accurate message counts and attributes in mailboxes for various users', function () {
    //iterate through each message and mailbox to test message counts and attributes are accurate
      seederData.mailboxes.forEach((mailbox) =>  {
        cy.login(mailbox.user.username, mailbox.user.password);
          cy.visit('/')

          cy.getBySel('home-mailbox-name').contains(mailbox.name).parent().within((el) => {
            cy.wrap(el).click();
            
            if (mailbox.count.hasOwnProperty('all_messages')) {
              cy.getBySel('home-mailbox-all').find('span').should('have.text', mailbox.count.all_messages);
            }

            if (mailbox.count.hasOwnProperty('unread_all')) {
              cy.getBySel('home-mailbox-unread-all').find('span').should('have.text', mailbox.count.unread_all);
            }

            if (mailbox.count.hasOwnProperty('unread_to_me')) {
              cy.getBySel('home-mailbox-unread-received').find('span').should('have.text', mailbox.count.unread_to_me);
            }

            if (mailbox.count.hasOwnProperty('unread_cc')) {
              cy.getBySel('home-mailbox-unread-copied').find('span').should('have.text', mailbox.count.unread_cc);
            }

            if (mailbox.count.hasOwnProperty('unread_query')) {
              cy.getBySel('home-mailbox-unread-query').find('span').should('have.text', mailbox.count.unread_query);
            }

            if (mailbox.count.hasOwnProperty('unread_replies')) {
              cy.getBySel('home-mailbox-unread-replies').find('span').should('have.text', mailbox.count.unread_replies);
            }

            if (mailbox.count.hasOwnProperty('read_all')) {
              cy.getBySel('home-mailbox-read-all').find('span').should('have.text', mailbox.count.read_all);
            }

            if (mailbox.count.hasOwnProperty('sent_all')) {
              cy.getBySel('home-mailbox-sent-all').find('span').should('have.text', mailbox.count.sent_all);
            }

            if (mailbox.count.hasOwnProperty('sent_replies')) {
              cy.getBySel('home-mailbox-sent-replies').find('span').should('have.text', mailbox.count.sent_replies);
            }
          });

          mailbox.messages.forEach((message) => {
            if (message.folder.hasOwnProperty('unread_all')) {
              testMessageAttributes(message, mailbox, '[data-test="home-mailbox-unread-all"]');
            }
  
            if (message.folder.hasOwnProperty('unread_to_me')) {
              testMessageAttributes(message, mailbox, '[data-test="home-mailbox-unread-received"]');
            }
  
            if (message.folder.hasOwnProperty('unread_cc')) {
              testMessageAttributes(message, mailbox, '[data-test="home-mailbox-unread-copied"]');
            }
  
            if (message.folder.hasOwnProperty('unread_query')) {
              testMessageAttributes(message, mailbox, '[data-test="home-mailbox-unread-query"]');
            }
  
            if (message.folder.hasOwnProperty('unread_replies')) {
              testMessageAttributes(message, mailbox, '[data-test="home-mailbox-unread-replies"]');
            }
  
            if (message.folder.hasOwnProperty('read_all')) {
              testMessageAttributes(message, mailbox, '[data-test="home-mailbox-read-all"]');
            }

            if (message.folder.hasOwnProperty('sent_all')) {
              testMessageAttributes(message, mailbox, '[data-test="home-mailbox-sent-all"]');
            }

            if (message.folder.hasOwnProperty('sent_replies')) {
              testMessageAttributes(message, mailbox, '[data-test="home-mailbox-sent_replies"]');
            }
          })
      })
  });

  function testMessageAttributes(message, mailbox, element) {
    cy.intercept('*messages=*').as('messages');

    cy.getBySel('home-mailbox-name').contains(mailbox.name).parent().find(element).click();
  
    cy.wait('@messages');

    cy.getBySel('messages-table').find(`[data-event-id="${message.event.id}"]`).within(($row) => {
      cy.wrap($row).find('[data-test="message-sender"]').contains(message.sender[0]);
      cy.wrap($row).find('[data-test="message-recipient"]').contains(message.sender[1]);
  
      cy.wrap($row).find('[data-test="home-mailbox-message-text"]').contains(message.text);
    })
  }

  it('ensure the dropdown on the homepage filters the search results as expected', function () {
    seederData.filters.forEach((filter) =>  {
      cy.login(filter.user.username, filter.user.password);
      cy.visit('/');
      cy.getBySel('search-mailbox-dropdown-filter').scrollIntoView();

      filter.criterias.forEach((criteria) => {
        //if mailbox has some value, only then select the option in the dropdown
        if (criteria.mailbox !== null) {
          cy.getBySel('search-mailbox-dropdown-filter').select(criteria.mailbox);
        }
        if (criteria.sender !== null) {
          cy.getBySel('search-sender-dropdown-filter').select(criteria.sender);
        }
        if (criteria.type !== null) {
          cy.getBySel('search-message-type-dropdown-filter').select(criteria.type);
        }
        
        // click on search button
        cy.getBySel('search-button-filter-submit').scrollIntoView();
        cy.getBySel('search-button-filter-submit').click();
        
        // assert the number of messages.
        cy.getBySel('messages-table').find('tbody tr').should('have.length', criteria.count);
      });
    });
  });

  it('verify the counts of started threads, messages waiting for query replies, and unread messages for each user', function () {
    seederData.user_mailbox_counts.forEach((count) =>  {
      cy.login(count.user.username, count.user.password);
      cy.visit('/');
      
      cy.getBySel('home-mailbox-name').contains(count.name).parent().within((el) => {
        cy.wrap(el).click();

        cy.getBySel('home-mailbox-sent-all-started-threads').find('span').should('have.text', count.started_threads);
        cy.getBySel('home-mailbox-sent-unreplied').find('span').should('have.text', count.waiting_for_query_reply);
        cy.getBySel('home-mailbox-sent-unread').find('span').should('have.text', count.unread_by_recipient);
      });
    });
  }); 
});
