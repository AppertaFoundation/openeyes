describe('behaviour of the allergy element', () => {
    beforeEach(() => {
        cy.login()
            .then(() => {
                return cy.createPatient();
            })
            .then((patient) => {
                return cy.getEventCreationUrl(patient.id, 'OphCiExamination')
                    .then((url) => {
                        cy.visit(url);
                        cy.removeElements([], true);
                        return cy.addExaminationElement('Allergies');
                    });
            });
    });

    it('adding other allergies from the list', () => {
        cy.get('#add-allergy-btn').click();

        cy.get('#history-allergy-popup')
            .should('be.visible')
            .within(() => {
                cy.get(`li[data-label="Other"]`).scrollIntoView();
                cy.get(`li[data-label="Other"]`).click();
                cy.get('.add-icon-btn').click();
            });
        cy.get('#OEModule_OphCiExamination_models_Allergies_entries_0_other').type("Foo");

        cy.get('#add-allergy-btn').click();
        cy.get('#history-allergy-popup')
            .should('be.visible')
            .within(() => {
                cy.get(`li[data-label="Other"]`).scrollIntoView();
                cy.get(`li[data-label="Other"]`).click();
                cy.get('.add-icon-btn').click();
            });
        cy.get('#OEModule_OphCiExamination_models_Allergies_entries_1_other').type("Bar");

        cy.saveEvent();
        cy.get('#js-listview-allergies-pro').within(() => {
            cy.get('td').should('contain', 'Foo');
            cy.get('td').should('contain', 'Bar');
        });

    });


})