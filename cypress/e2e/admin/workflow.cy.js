describe('behaviour of the admin screen for workflows and workflow rule', () => {

    beforeEach(() => {
        cy.login();
    });

    it('check add contact label has Is Private and Max Number Per Patient ', function () {

        cy.visit('/OphCiExamination/admin/addWorkflow');

        cy.getBySel('institutions_list').select('The Monachs Trust');

        cy.getBySel('name').type('The Monachs Trust Test');

        cy.get('#et_save').click();

        cy.visit('/OphCiExamination/admin/addWorkflow');

        cy.getBySel('institutions_list').select('Holby City NHS Foundation Trust');

        cy.getBySel('name').type('Holby City NHS Foundation Trust Test');

        cy.get('#et_save').click();

        cy.visit(`/OphCiExamination/admin/addWorkflowRule`);

        cy.getBySel('workflow').should('contain', 'The Monachs Trust Test');

        cy.getBySel('workflow').should('not.contain', 'Holby City NHS Foundation Trust Test');

        cy.getBySel('institution').select('Holby City NHS Foundation Trust');

        cy.getBySel('workflow').should('contain', 'Holby City NHS Foundation Trust Test');

        cy.getBySel('workflow').should('not.contain', 'The Monachs Trust Test');

    });

});