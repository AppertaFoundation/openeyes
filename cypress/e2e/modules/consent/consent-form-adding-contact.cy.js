describe('consent adding contact', () => {
    beforeEach(() => {
        cy.login()
            .then(() => {
                return cy.createPatient();
            })
            .then((patient) => {
                return cy.getEventCreationUrl(patient.id, 'OphTrConsent');
            }).then((url) => {
            cy.visit(url);
        })
    });

    it('able to add contact in consent that has no address', () => {
        cy.get('input#template1_right_eye').click();
        cy.get('button.booking-select').contains('Consent').first().click();
        cy.getBySel('consent-type').select("4");
        cy.getBySel('consent-paper-copies').find('[type="radio"]').last().click();
        cy.getBySel('consent-lack-of-capacity-reasons').first().click();
        cy.get('input#Element_OphTrConsent_Procedure_AnaestheticType_LA').click();

        //Start of adder dialog
        cy.getBySel('consent-add-contact').click();

        cy.intercept('OphTrConsent/contact/OpeneyesContactsWithUser*').as('contactsWithUser');

        cy.selectAdderDialogOptionAdderID('contact_adder_type', 'Openeyes users');
        cy.wait('@contactsWithUser');
        cy.selectAdderDialogOptionAdderID('contact_adder_method', 'Face to face');

        cy.selectAdderDialogOptionText('Dr Michael Morgan (Staff)');
        cy.confirmAdderDialog();

        cy.getBySel('event-action-save-and-print').first().click();
        cy.assertEventSaved();
    });
});
