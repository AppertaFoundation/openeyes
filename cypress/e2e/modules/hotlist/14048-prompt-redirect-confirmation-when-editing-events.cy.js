// Covers functionality introduced by OE-13040 - templates for operation notes
describe('Test user confirmation prompt when the user is about to be redirected under event editing context (create/update)', () => {
    beforeEach(() => {
        cy.login()
            .then(() => {
                return cy.createPatient();
            })
            .then((patient) => {
                return cy.getEventCreationUrl(patient.id, 'OphCiExamination');
            })
            .then((url) => {
                cy.visit(url);
            });
    });

    it('under an event editing context, if the user clicks on any patient link through the hotlist and select cancel in the popup, the page should not be redirected', () => {
        // hover over the hotlist btn to make the hotlist show
        cy.getBySel("hotlist-btn").trigger('mouseover');

        cy.getBySel("hotlist-patient").then(hotlist_patients => {
            // assert the popup texts and select cancel
            cy.on("window:confirm", (text) => {
                expect(text).to.contains('Are you sure that you wish to leave the page');
                return false;
            });

            cy.wrap(hotlist_patients).first().click();
        })
    });

    it('under an event editing context, if the user clicks on any patient link through the hotlist and select ok in the popup, the page should be redirected', () => {
        // hover over the hotlist btn to make the hotlist show
        cy.getBySel("hotlist-btn").trigger('mouseover');

        cy.getBySel("hotlist-patient").then(hotlist_patients => {
            // assert the popup texts and select ok
            cy.on("window:confirm", (text) => {
                expect(text).to.contains('Are you sure that you wish to leave the page');
                return true;
            });

            cy.wrap(hotlist_patients).first().click()
            .then(() => {
                // should redirect
                cy.url().should('include', '/patient/summary');
            });
        })
    });

    it('under an event editing context, if the user searches for a patient through the hotlist and select cancel in the popup, the page should not be redirected', () => {
        // hover over the hotlist btn to make the hotlist show
        cy.getBySel("hotlist-btn").trigger('mouseover');

        cy.getBySel("hotlist-find-patient-btn").then(search_btn => {
            // assert the popup texts and select cancel
            cy.on("window:confirm", (text) => {
                expect(text).to.contains('Are you sure that you wish to leave the page');
                return false;
            });

            cy.wrap(search_btn).first().click();
        })
    });

    it('under an event editing context, if the user searches for a patient through the hotlist and select ok in the popup, the page should be redirected', () => {
        // hover over the hotlist btn to make the hotlist show
        cy.getBySel("hotlist-btn").trigger('mouseover');

        cy.getBySel("hotlist-find-patient-btn").then(search_btn => {
            // assert the popup texts and select ok
            cy.on("window:confirm", (text) => {
                expect(text).to.contains('Are you sure that you wish to leave the page');
                return true;
            });

            cy.wrap(search_btn).first().click()
            .then(() => {
                // should redirect
                cy.url().should('not.contain', '/default/create');
            });
        })
    });
});