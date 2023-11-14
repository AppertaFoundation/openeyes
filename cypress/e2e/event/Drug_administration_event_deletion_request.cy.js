describe('Drug Administration Event should be uneditable when it is locked for deletion approval', () => {
    
    //Creating a prescriber user with no admin permissions and adding a PSD Setting
    beforeEach(function () {
        cy.login()
        .then((body) => {
            return cy.runSeeder('OphDrPGDPSD', 'AddPGDPSDSettingSeeder', {admin_firm_id: body.body.firm_id})
        }).as('seederData')
    });
  
    it('Check Drug Administration Event locks down and cannot be edited pending deletion approval', function () {
        cy.login(this.seederData.user.username, this.seederData.user.password);
        cy.getEventCreationUrl(this.seederData.patientId, 'OphDrPGDPSD').then((url) => {
            cy.visit(url);

            // Add a preset order by selecting a PSD and a Laterality (Right & Left Eyes)
            cy.getBySel('add-preset-order-button').click();
            cy.getBySel('adder-dialog').filter(':visible').within(($el) => {
                cy.get($el).find('[data-test="add-options"]').find(`[data-label="${this.seederData.pgdPsd.name}"]`).click();
                cy.get($el).find('[data-test="add-options"]').find(`[data-name="both"]`).click();
            });
    
            //Click on "click to add" button
            cy.getBySel('add-icon-btn').filter(':visible').click();

            //If the "Assign this Preset Order Button" is visible then click 
            cy.getBySel('drug-administration-element-fields').then(($ele) => {
                if ($ele.find('[data-test="assign-and-confirm-preset-order-button"]').length > 0) {
                    cy.getBySel('assign-and-confirm-preset-order-button').click();
                }
            })

            //Save the event
            cy.getBySel('event-action-save').first().click();

            //Delete the event
            cy.getBySel('event-action-').click();
            cy.getBySel('reason-for-deletion').type('Test event deletion');
            cy.getBySel('delete-event').click();

            //The "Edit" button should not be visible when the event is pending deletion and has been locked
            cy.getBySel('button-header-tab-Edit').should('not.be', 'visible');
        });

        cy.getEventCreationUrl(this.seederData.patientId, 'OphCiExamination').then((url) => {
            // The Drug Administration block should not be visible in an Examination event after it is locked for deletion approval
            cy.visit(url);
            cy.removeElements();
            cy.addExaminationElement('Drug Administration');

            //Check the drug administration element should be empty because drug administration event is pending deletion and has been locked
            cy.getBySel('Drug-Administration-element-section').find('.order-block').should('not.exist');
        });
    });
});
 