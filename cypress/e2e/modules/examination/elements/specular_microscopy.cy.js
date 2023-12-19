describe('behaviour of the specular microscopy element', () => {
    before(() => {
        cy.login();
        cy.createPatient().as('patient').then(patient => {
            cy.getEventCreationUrl(patient.id, 'OphCiExamination').as('createUrl')
                .then(url => {
                    cy.visit(url);
                    cy.removeElements('Specular Microscopy', true);
                });
        });
    });

    it('saves single side', () => {
        cy.addExaminationElement('Specular Microscopy');
        cy.removeElementSide('Specular Microscopy', 'left');
        cy.getBySel(`Specular-Microscopy-element-section`).within(section => {
            cy.getBySel(`right-endothelial-cell-density-value`).type(510);
            cy.getBySel(`right-coefficient-variation-value`).type(2);
        });

        cy.saveEvent();
        cy.assertEventSaved();
    });
});
