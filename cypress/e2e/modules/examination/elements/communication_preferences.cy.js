describe('communication preferences element behaviour', () => {
    beforeEach(() => {
        cy.login();
    });

    it('shows the correct other text labels for language and interpreter when saved with those values', () => {
        cy.createModels('OEModule\\OphCiExamination\\models\\Element_OphCiExamination_CommunicationPreferences', ['withOtherLanguage', 'withOtherInterpreterRequired'])
            .then((element) => {
                cy.visit(element.event.urls.view);
                cy.getBySel('language-value').should('have.text', 'Other');
                cy.getBySel('interpreter-value').should('have.text', 'Other');
            });
    });

    it('shows the selected language names when chosen', () => {
        cy.createModels('OEModule\\OphCiExamination\\models\\Element_OphCiExamination_CommunicationPreferences')
            .then((element) => {
                cy.visit(element.event.urls.view);
                cy.getBySel('language-value').should('have.text', element.language.name);
                cy.getBySel('interpreter-value').should('have.text', element.interpreter_required.name);
            });
    });
});
