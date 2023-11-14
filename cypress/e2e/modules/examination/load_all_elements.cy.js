describe('Load all examination elements', () => {
    let seederData;

    beforeEach(() => {
        cy.login();
    });

    before(() => {
        cy.login()
            .then(() => {
                return cy.runSeeder('OphCiExamination', 'LoadAllElementsSeeder');
            })
            .then((data) => {
                seederData = data;
            });
    })

    it('loads all examination elements via dialog', function() {
        cy.visitEventCreationUrl(seederData.patient.id, 'OphCiExamination')
        .then(() => {
            cy.intercept({
                method: 'GET',
                url: '/OphCiExamination/Default/ElementForm*'
            }).as('ElementForm');

            cy.removeElements();
            cy.addExaminationElement(seederData.elements, false);
            cy.wait('@ElementForm');

            seederData.elements.forEach((element_name) => {
                cy.getBySel('element-title').should('contain', element_name);
            });
        });
    });
});