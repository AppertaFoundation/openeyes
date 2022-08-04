describe('contacts widget behaviour', () => {
    before(() => {
        cy.login()
            .then(() => {
                return cy.createPatient();
            })
            .then((patient) => {
                return cy.getEventCreationUrl(patient.id, 'OphCiExamination')
                    .then((url) => {
                        return [url, patient];
                    });
            })
            .then(([url, patient]) => {
                cy.visit(url);
                cy.addExaminationElement('Contacts');
            });
    });

    it('adding new contact correctly calls endpoint', () => {

        cy.intercept('/OphCiExamination/contact/saveNewContact').as('saveNewContact')


        cy.get('#add-contacts-btn').click();

        cy.get('#contacts-popup')
            .should('be.visible') 
            .within(() => {
                // Select next of kin
                cy.get('li[data-id="96"]').should('be.visible');
                cy.get('li[data-id="96"]').click();

                // Select add new
                cy.get('li[data-type="custom"]').should('be.visible');
                cy.get('li[data-type="custom"]').click();

                cy.get('.add-icon-btn').click();
            })
            .then(() => {
                cy.get('.oe-popup')
                    .should('be.visible')
                    .within(() => {
                        cy.get('[data-label="first_name"]').type('Foo');
                        cy.get('[data-label="last_name"]').type('Bar');
                        cy.get('[data-label="email"]').type('foo.bar@example.com');
                        cy.get('[data-label="primary_phone"]').type('0123 456 789');

                        cy.get('input[type="submit"]').click();
                    });
            });        

        cy.wait('@saveNewContact');

        cy.get('@saveNewContact').its('callCount').should('equal', 1);
    });
});