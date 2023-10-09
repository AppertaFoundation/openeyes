describe('tests to check add patient functionality for patient identifier section', () => {
    before(() => {
        cy.login();
        cy.visit('admin/editinstitution?institution_id=1');
        cy.getBySel('add-new-display-preference').click();
        cy.selectAdderDialogOptionText('Genetics Subject ID (National Health Service)');
        cy.confirmAdderDialog();
        cy.getBySel('necessity-dropdown').each(function (necessityDropdown) {
            cy.wrap(necessityDropdown).select('Optional');
        });

        cy.get('#et_save').click();
        cy.visit('/patient/create');
    });

    it('shows identifier in the same spot after adding an identifier with multiple identifier types present', () => {
        cy.generateRandomString(6).then((randomString) => {
            cy.getBySel('first-name').type(randomString);
            cy.getBySel('last-name').type(randomString);
            cy.getBySel('dob').type('02/01/2019');
            cy.getBySel('country').select('2');

            cy.getBySel('phone').type('12345678');
        });

        const randomIdentifier = Cypress._.random(100000, 900000);

        cy.get('.js-patient-identifier-duplicate-check-1').find('input[type="text"]').type(randomIdentifier);
        cy.getBySel('save-patient').click();

        cy.getBySel('no-pedigree').click();
        cy.get('#et_save').click();
        cy.get('.oe-popup-content').find('.ok').click();


        cy.getBySel('edit-local-patient-button').click();
        cy.get('.js-patient-identifier-duplicate-check-1').find('input[type="text"]').should('have.value', randomIdentifier);
    });

    after(() => {
        cy.visit('admin/editinstitution?institution_id=1');

        cy.getBySel('patient-identifiers-entry-table').within(() => {
            cy.getBySel('identifier-short-title').contains('Genetics Subject ID').closest('tr').find('.js-delete_patient_identifier').click();
        })

        cy.getBySel('necessity-dropdown').each(function (necessityDropdown) {
            cy.wrap(necessityDropdown).select('Hidden');
        });

        cy.get('#et_save').click();
    });
});