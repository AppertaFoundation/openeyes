describe('Operation booking whiteboard tests', () => {
    describe('Whiteboard target refraction tests', () => {

        beforeEach(() => {
            cy.login();
        });

        it('Predicted and target differs more than 0.5D and shows warning ', () => {
            const predictedRefraction = '3.00';
            const targetRefraction = '2.00';
            const eyeId = 3;

            cy.runBiometryAndOperationBookingSeedersAndVisitOpBooking(eyeId, predictedRefraction, targetRefraction);

            cy.getBySel('event-action-whiteboard').invoke("attr", "href")
                .then(href => {
                    cy.visit(href);
                    const predictedOutcomeCard = cy.get('.oe-wb-widget').find('h3')
                        .contains('Predicted Outcome').closest('.oe-wb-widget');

                    predictedOutcomeCard.should('have.class', 'orange');

                    predictedOutcomeCard.find('.wb-data')
                        .should('contain.text', "Target refraction")
                        .closest('.wb-data')
                        .should('contain.text', "2.00 D");
                });
        });

        it('Predicted and target differs more than 0.5D and does not show warning ', () => {
            const predictedRefraction = '-3.00';
            const targetRefraction = '-2.50';
            const eyeId = 3;

            cy.runBiometryAndOperationBookingSeedersAndVisitOpBooking(eyeId, predictedRefraction, targetRefraction);

            cy.getBySel('event-action-whiteboard').invoke("attr", "href")
                .then(href => {
                    cy.visit(href);
                    const predictedOutcomeCard = cy.get('.oe-wb-widget').find('h3')
                        .contains('Predicted Outcome').closest('.oe-wb-widget');

                    predictedOutcomeCard.should('not.have.class', 'orange');

                    predictedOutcomeCard.find('.wb-data')
                        .should('contain.text', "Target refraction")
                        .closest('.wb-data')
                        .should('contain.text', "2.50 D");
                });
        });


        it('Predicted and target differs more than 0.5D and does not show warning ', () => {
            const predictedRefraction = '3.00';
            const eyeId = 3;

            cy.runBiometryAndOperationBookingSeedersAndVisitOpBooking(eyeId, predictedRefraction);

            cy.getBySel('event-action-whiteboard').invoke("attr", "href")
                .then(href => {
                    cy.visit(href);
                    const predictedOutcomeCard = cy.get('.oe-wb-widget').find('h3')
                        .contains('Predicted Outcome').closest('.oe-wb-widget');

                    predictedOutcomeCard.should('not.have.class', 'orange');

                    predictedOutcomeCard.find('.wb-data')
                        .should('not.contain.text', "Target refraction");
                });
        });
    });
});
