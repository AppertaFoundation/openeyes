describe('the behaviour of the list view control in create/update screens', () => {
    beforeEach(() => {
        cy.login();

        cy.runSeeder('OphCiExamination', 'ListViewControlSeeder').as('seederData');
    });

    it('operates on event view screens', function () {
        cy.visit(this.seederData.existingEvent.urls.view);

        cy.getBySel('listview-allergies-pro').should('be.visible');
        cy.getBySel('listview-allergies-full').should('not.be.visible');

        cy.getBySel('listview-expand-btn', '[data-list="allergies"]').click();
        cy.getBySel('listview-allergies-pro').should('not.be.visible');
        cy.getBySel('listview-allergies-full').should('be.visible');

        cy.getBySel('listview-expand-btn', '[data-list="allergies"]').click();
        cy.getBySel('listview-allergies-pro').should('be.visible');
        cy.getBySel('listview-allergies-full').should('not.be.visible');
    });

    it('operates on elements added after the initial create event page load', function () {
        cy.visitEventCreationUrl(this.seederData.existingEvent.patient.id, 'OphCiExamination');

        cy.removeElements([], true);
        cy.addExaminationElement('IOP History');

        for (let side of ['left', 'right']) {
            cy.getBySel(`listview-historyiop-${side}`).should('not.be.visible');

            cy.getBySel('listview-expand-btn', `[data-list="historyiop-${side}"]`).click();
            cy.getBySel(`listview-historyiop-${side}`).should('be.visible');

            cy.getBySel(`listview-historyiop-${side}`).contains(this.seederData.historyIOP[side]);

            cy.getBySel('listview-expand-btn', `[data-list="historyiop-${side}"]`).click();
            cy.getBySel(`listview-historyiop-${side}`).should('not.be.visible');
        }
    });
});
