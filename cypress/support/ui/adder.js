/**
 * A Series of commands for working with adder dialogs.
 */
Cypress.Commands.add('selectAdderDialogOptionText', (string) => {
    cy.get('div.oe-add-select-search').contains(string).scrollIntoView().click()
})

Cypress.Commands.add('assertAdderDialogIncludes', (text) => {
    cy.get('[data-test="adder-dialog"]:visible').should('contain', text)
})

Cypress.Commands.add('assertAdderDialogDoesNotInclude', (text) => {
    cy.get('[data-test="adder-dialog"]:visible').should('not.contain', text)
})

Cypress.Commands.add('selectAdderDialogOptionText', (text) => {
    cy.get('[data-test="adder-dialog"]').contains(text).scrollIntoView().click()
});

Cypress.Commands.add('selectAdderDialogOptionByDataLabel', (text) => {
    cy.get('[data-test="adder-dialog"]:visible').find(`li[data-label="${text}"]`).scrollIntoView().click()
})

Cypress.Commands.add('selectAdderDialogOptionVariable', (variable, text) => {
    cy.get(variable).contains(text).scrollIntoView().click()
})

Cypress.Commands.add('selectAdderDialogOptionAdderID', (headingId, text) => {
    cy.get('[data-adder-id="' + headingId + '"]').contains(text).click()
})

Cypress.Commands.add('confirmAdderDialog', () => {
    cy.get('div[data-test="adder-dialog"]:visible > div.add-icon-btn').contains('Click to add').click({force: true})
})

Cypress.Commands.add('cancelAdderDialog', () => {
    cy.get('div[data-test="adder-dialog"]:visible > div.close-icon-btn').click({force: true})
})


Cypress.Commands.add('dialogCorrectOptionsShownOnOpenAndAfterOptionsSelected', (elementId,
                                                                                adderButtonSelector,
                                                                                visibleColumnIds, hiddenColumnIds,
                                                                                toSelectOptionsText, visibleColumnIdsAfterSelect,
                                                                                hiddenColumnIdsAfterSelect, side = null) => {

    cy.getBySel(elementId).scrollIntoView().within(() => {
        if (side !== null) {
            cy.get(`.js-element-eye.${side}-eye`).within(() => {
                cy.getBySel(adderButtonSelector).click();
            });
        } else {
            cy.getBySel(adderButtonSelector).click();
        }
    });

    cy.assertVisibleAndHiddenColumnsBasedById(visibleColumnIds, hiddenColumnIds);

    toSelectOptionsText.forEach((toSelectOptionText) => {
        cy.selectAdderDialogOptionByDataLabel(toSelectOptionText);
    });

    cy.assertVisibleAndHiddenColumnsBasedById(visibleColumnIdsAfterSelect, hiddenColumnIdsAfterSelect);
    cy.cancelAdderDialog();
});

Cypress.Commands.add('assertVisibleAndHiddenColumnsBasedById', (visibleColumnIds, hiddenColumnIds) => {
    cy.get('[data-test="adder-dialog"]:visible').within(() => {
        visibleColumnIds.forEach((visibleColumnId) => {
            cy.get(`[data-id="${visibleColumnId}"]`).should('be.visible');
            cy.get(`[data-adder-id="${visibleColumnId}"]`).should('be.visible');
        });

        hiddenColumnIds.forEach((visibleColumnId) => {
            cy.get(`[data-id="${visibleColumnId}"]`).should('be.not.visible');
            cy.get(`[data-adder-id="${visibleColumnId}"]`).should('be.not.visible');
        });
    });
})