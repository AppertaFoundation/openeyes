describe('Add queue set', function () {

    before(() => {
        cy.login()
            .then(() => {

                cy.visit('/PatientTicketing/admin/');

                const testQueueSetName1 = 'Test Queue Set 1';
                const testQueueSetName2 = 'Test Queue Set 2';
                cy.wrap(testQueueSetName1).as("testQueueSetName1");
                cy.wrap(testQueueSetName2).as("testQueueSetName2");

                cy.getBySel(`patient-ticketing-list`).within(table => {
                    cy.get('input[type="checkbox"]').check({force: true});
                });

                cy.getBySel(`admin-map-remove`).click();

                cy.addQueueSet({name: testQueueSetName1, addToInstitution: true});
                cy.addQueueSet({name: testQueueSetName2});

                return cy.createPatient().as('patient');
            })
            .then((patient) => {
                return cy.getEventCreationUrl(patient.id, 'OphCiExamination', 302); // Glaucoma Clinic
            }).as('createUrl');
    });

    // Make sure that there is at least 1 queue set left assigned to the institution, otherwise it causes some other tests to fail
    // that expect a patient ticket list to exist. This would not be necessary if the other tests used a factory / seeder...
    after(() => {
        cy.login()
            .then(() => {
                cy.visit('/PatientTicketing/admin/');
                cy.getBySel(`patient-ticketing-list`).within(table => {
                    cy.get('input[type="checkbox"]').check({ force: true });
                });
                cy.getBySel(`admin-map-add`).click();
            });
    });

    context('Only one queue set assigned to the institution', () => {

        beforeEach(function() {
            cy.login();
            cy.visit(this.createUrl);
        });

        it('should load the only available Queue Set in the institution', function () {
            cy.addExaminationElement('Follow-up');
            cy.getBySel('show-follow-up-adder').click();
            cy.selectAdderDialogOptionText('Virtual Review');
            cy.getBySel(`add-followup-btn`).click();

            cy.contains('Test Queue Set');
        });
    });

    context('Multiple queue sets are assigned to the institution', () => {
        before(function() {
            cy.login().then(() => {
                cy.visit('/PatientTicketing/admin/');
                const testQueueSetName3 = 'Test Queue Set 3';
                cy.wrap(testQueueSetName3).as("testQueueSetName3");
                cy.addQueueSet({name: "Test Queue Set 3", addToInstitution: true});
            });
        });

        it('should list the available Queue Sets to the institution', function() {
            cy.visit(this.createUrl);
            cy.addExaminationElement('Follow-up');
            cy.getBySel('show-follow-up-adder').click();
            cy.selectAdderDialogOptionText('Virtual Review');
            cy.getBySel(`add-followup-btn`).click();

            cy.get('#patientticket_queue option').should('have.length', 3);
            cy.get('#patientticket_queue').should('contain', 'Select');
            cy.get('#patientticket_queue').should('contain', this.testQueueSetName1);
            cy.get('#patientticket_queue').should('contain', this.testQueueSetName3);
        });
    });

    context('No queue sets are assigned to the institution', () => {
        before(function() {
            cy.login().then(() => {
                cy.visit('/PatientTicketing/admin/');

                cy.getBySel(`patient-ticketing-list`).within(table => {
                    cy.get('input[type="checkbox"]').check({force: true});
                });

                cy.getBySel(`admin-map-remove`).click();
            });
        });

        it('should display a message that there is no queue set', function()  {
            cy.visit(this.createUrl);
            cy.addExaminationElement('Follow-up');
            cy.getBySel('show-follow-up-adder').click();
            cy.selectAdderDialogOptionText('Virtual Review');
            cy.getBySel(`add-followup-btn`).click();

            cy.contains('No valid Virtual Clinics available');
        });
    });

});
