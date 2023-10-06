describe('behaviour of the advice given element', () => {
    beforeEach(() => {
        cy.login();

        cy.runSeeder('OphCiExamination', 'AdviceGivenLeafletsSeeder').as('seederData');
    });

    it('either shows all leaflets when no category selected or just leaflets from the selected category', function () {
        cy.login(this.seederData.user.username, this.seederData.user.password, this.seederData.site.id, this.seederData.institution.id);

        cy.visitEventCreationUrl(this.seederData.patient.id, 'OphCiExamination');

        // Because a specific firm is set for the created user (instead of having global rights) without specific assignments in its profile,
        // a popup comes up. The following dismisses it.
        cy.getBySel('set-site-and-firm-later-button').click();

        cy.removeElements();
        cy.addExaminationElement('Advice Given');

        cy.getBySel('add-leaflet-btn').click();

        cy.getBySel('add-options', '[data-id="leaflet"]').within(() => {
            for (const leaflet of this.seederData.firstCategoryLeaflets) {
                cy.get(`[data-id="${leaflet.id}"][data-label="${leaflet.name}"]`).should('have.length', 1);
            }

            for (const leaflet of this.seederData.secondCategoryLeaflets) {
                cy.get(`[data-id="${leaflet.id}"][data-label="${leaflet.name}"]`).should('have.length', 1);
            }

            cy.get(`[data-id="${this.seederData.bothCategoriesLeaflet.id}"][data-label="${this.seederData.bothCategoriesLeaflet.name}"]`).should('have.length', 1);
        });

        cy.getBySel('add-options', '[data-id="leaflet-category"]').within(() => cy.get(`[data-id="${this.seederData.firstCategory.id}"]`).click());

        cy.getBySel('add-options', '[data-id="leaflet"]').within(() => {
            for (const leaflet of this.seederData.firstCategoryLeaflets) {
                cy.get(`[data-id="${leaflet.id}"][data-label="${leaflet.name}"]`).should('have.length', 1);
            }

            for (const leaflet of this.seederData.secondCategoryLeaflets) {
                cy.get(`[data-id="${leaflet.id}"][data-label="${leaflet.name}"]`).should('have.length', 0);
            }

            cy.get(`[data-id="${this.seederData.bothCategoriesLeaflet.id}"][data-label="${this.seederData.bothCategoriesLeaflet.name}"]`).should('have.length', 1);
        });

        cy.getBySel('add-options', '[data-id="leaflet-category"]').within(() => cy.get(`[data-id="${this.seederData.secondCategory.id}"]`).click());

        cy.getBySel('add-options', '[data-id="leaflet"]').within(() => {
            for (const leaflet of this.seederData.firstCategoryLeaflets) {
                cy.get(`[data-id="${leaflet.id}"][data-label="${leaflet.name}"]`).should('have.length', 0);
            }

            for (const leaflet of this.seederData.secondCategoryLeaflets) {
                cy.get(`[data-id="${leaflet.id}"][data-label="${leaflet.name}"]`).should('have.length', 1);
            }

            cy.get(`[data-id="${this.seederData.bothCategoriesLeaflet.id}"][data-label="${this.seederData.bothCategoriesLeaflet.name}"]`).should('have.length', 1);
        });

        // Deselect by clicking again - this should bring back the entire list
        cy.getBySel('add-options', '[data-id="leaflet-category"]').within(() => cy.get(`[data-id="${this.seederData.secondCategory.id}"]`).click());

        cy.getBySel('add-options', '[data-id="leaflet"]').within(() => {
            for (const leaflet of this.seederData.firstCategoryLeaflets) {
                cy.get(`[data-id="${leaflet.id}"][data-label="${leaflet.name}"]`).should('have.length', 1);
            }

            for (const leaflet of this.seederData.secondCategoryLeaflets) {
                cy.get(`[data-id="${leaflet.id}"][data-label="${leaflet.name}"]`).should('have.length', 1);
            }

            cy.get(`[data-id="${this.seederData.bothCategoriesLeaflet.id}"][data-label="${this.seederData.bothCategoriesLeaflet.name}"]`).should('have.length', 1);
        });
    });

    it('permits adding a leaflet without choosing a category', function () {
        cy.login(this.seederData.user.username, this.seederData.user.password, this.seederData.site.id, this.seederData.institution.id);

        cy.visitEventCreationUrl(this.seederData.patient.id, 'OphCiExamination');

        // Because a specific firm is set for the created user (instead of having global rights) without specific assignments in its profile,
        // a popup comes up. The following dismisses it.
        cy.getBySel('set-site-and-firm-later-button').click();

        cy.removeElements();
        cy.addExaminationElement('Advice Given');

        cy.getBySel('add-leaflet-btn').click();

        cy.getBySel('add-options', '[data-id="leaflet"]').within(() => {
            cy.get(`[data-id="${this.seederData.bothCategoriesLeaflet.id}"][data-label="${this.seederData.bothCategoriesLeaflet.name}"]`).click();
        });

        cy.confirmAdderDialog();

        cy.getBySel('leaflet-entry', `[data-id="${this.seederData.bothCategoriesLeaflet.id}"][data-label="${this.seederData.bothCategoriesLeaflet.name}"]`);
    });
});
