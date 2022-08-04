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
                return cy.addExaminationElement('Contacts');
            });
    });

    it('adding new contact correctly calls endpoint', () =>{

        cy.intercept('/OphCiExamination/contact/saveNewContact').as('saveNewContact')

        cy.get('#add-contacts-btn').click();

        cy.get('#contacts-popup')
            .should('be.visible') 
            .within(() => {
                return cy.getModelByAttributes('ContactLabel', {name: 'Next of Kin'})
                    .then((contactLabel) => {
                        // Select next of kin
                        cy.get(`li[data-id="${contactLabel.id}"]`).scrollIntoView();
                        cy.get(`li[data-id="${contactLabel.id}"]`).click();

                        // Select add new
                        cy.get('li[data-type="custom"]').should('be.visible');
                        cy.get('li[data-type="custom"]').click();

                        cy.get('.add-icon-btn').click();
                    })
                
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

        cy.get('@saveNewContact.all').then((interceptions) => {
            expect(interceptions).to.have.length(1);
        });
    });
});