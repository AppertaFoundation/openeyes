describe('verifies prescription secondary e-sign signatories behaviour', () => {

    const itDisplaysSignatoriesInOrder = (signatory_names) => {

        cy.getBySel('signatory-list-table').find('tbody').within(tbody => {
            cy.get('tr').should('have.length', signatory_names.length);
            cy.get('tr').each(($row, index) => {
                if (index < signatory_names.length) {
                    cy.wrap($row)
                        .find('td:eq(1)')
                        .invoke('text')
                        .then((text) => {
                            const expectedSignatory = signatory_names[index].trim();
                            const actualSignatory = text.trim();
                            expect(actualSignatory).to.equal(expectedSignatory);
                        });
                }
            });
        });
    };

    const itRemovesSignatureByRole = ($table, role) => {
        $table.contains(role).closest('tr').within(tr => {
            cy.getBySel('remove-sign-btn').then($button => $button.show()).click();
        });
    };

    const fillPrescriptionEventForm = () => {
        cy.get(`#add-prescription-btn`).click();
        cy.getBySel(`adder-dialog`).should('be.visible');

        cy.get(`[data-test="add-options"][data-id="common-opthalmic"] li`).first().click();
        cy.getBySel(`add-icon-btn`).filter(':visible').click();

        cy.getBySel(`prescription-items`, ' tbody tr')
        .each((element, index) => {
            cy.get(`#Element_OphDrPrescription_Details_items_${index}_dose`).type(1);
            cy.get(`#Element_OphDrPrescription_Details_items_${index}_dose_unit_term`).select(1);
            cy.get(`#Element_OphDrPrescription_Details_items_${index}_route_id`).select('Eye');
            cy.get(`#Element_OphDrPrescription_Details_items_${index}_laterality`).select(1);
            cy.get(`#Element_OphDrPrescription_Details_items_${index}_frequency_id`).select(1);
            cy.get(`#Element_OphDrPrescription_Details_items_${index}_duration_id`).select(1);
            cy.get(`#Element_OphDrPrescription_Details_items_${index}_dispense_condition_id`).select(1);
        });
    };

    const REQUIRE_PIN_SIGN_SETTING = 'require_pin_for_prescription';

    before(function() {
        cy.wrap(["Prescriber", "Assessed by", "Administered by", "Verified by", "Instructed by"]).as('signatory_names');
        cy.wrap("Administered by").as("test_sign_role");
        cy.login()
            .then(() => {
                cy.setSystemSettingValue(REQUIRE_PIN_SIGN_SETTING, '0');
                return cy.runSeeder('OphDrPrescription', 'PrescriptionPINSecondarySignSeeder', {
                    "secondary_signatories": this.signatory_names.slice(1), //skip Prescriber
                    "delete_existing": true,
                    "institution_remote_id": null
                });
            }).as('seederData');
    });

    context('when PIN is not required for sign', () => {
        before(function () {
            cy.login()
                .then(() => {
                    return cy.createPatient();
                })
                .then((patient) => {
                    return cy.getEventCreationUrl(patient.id, 'OphDrPrescription')
                        .then((url) => {
                            cy.visit(url);
                        });
                });
        });

        beforeEach(() => {
            cy.login();
        });

        it('only displays Prescriber signature on create page and saves the event', function() {
            fillPrescriptionEventForm();

            cy.getBySel('signature-wrapper').should('be.visible');

            cy.getBySel('signatory-list-table')
                .find('tbody tr')
                .should('have.length', 1)
                .find('span.js-signatory-label')
                .should('contain', 'Prescriber');

            cy.getBySel('event-action-save').first().click();
            cy.url().as('eventViewUrl', { type: 'static' });
        });

        it('lists secondary signatories in order, completes and removes sign on button click', function() {
            cy.visit(this.eventViewUrl);

            itDisplaysSignatoriesInOrder(this.signatory_names);

            cy.intercept('/OphDrPrescription/default/getSignatureForLoggedInUser*').as('signatureRequest');

            cy.getBySel('signatory-list-table')
                .contains(this.test_sign_role)
                .closest('tr')
                .within($tr => {

                    cy.getBySel('complete-sign-btn').click();
                    cy.wait('@signatureRequest');

                    cy.getBySel('signature-wrapper').should('be.visible');
                    cy.contains(this.seederData.logged_in_user_name);
                });

            cy.intercept('OphDrPrescription/default/removeSignature*').as('removeSignatureRequest');
            itRemovesSignatureByRole(cy.getBySel('signatory-list-table'), this.test_sign_role);
            cy.wait('@removeSignatureRequest');

            cy.getBySel('signatory-list-table')
                .contains(this.test_sign_role)
                .closest('tr').within(($tr) => {
                    cy.wrap($tr).should('not.contain', this.seederData.logged_in_user_name);
                    cy.getBySel('complete-sign-btn').should('be.visible');
            });

            itDisplaysSignatoriesInOrder(this.signatory_names);
        });
    });

    context('when PIN is required for sign', () => {
        before(function () {
            cy.wrap("Verified by").as("test_sign_role");

            cy.login()
                .then(() => {
                    cy.setSystemSettingValue(REQUIRE_PIN_SIGN_SETTING, '1');
                    cy.createPatient()
                        .then(patient => {
                            cy.getEventCreationUrl(patient.id, 'OphDrPrescription')
                                .then((url) => {
                                    cy.visit(url);
                                });
                        });
                });
        });

        beforeEach(function() { cy.login().as('loggedInUser'); });

        it('only displays Prescriber signature on create page and saves the event', function() {

            fillPrescriptionEventForm();

            cy.getBySel('signature-wrapper').should('not.be.visible');
            cy.getBySel('signature-control-widget').should('be.visible');

            cy.getBySel('signatory-list-table')
                .find('tbody tr')
                .should('have.length', 1)
                .find('span.js-signatory-label')
                .should('contain', 'Prescriber');

            cy.getBySel('event-action-save-draft').first().click();
            cy.url().as('eventViewUrl', { type: 'static' });
        });

        it('lists secondary signatories in order, completes sign by PIN and removes it on button click', function() {
            cy.visit(this.eventViewUrl);

            cy.intercept('/OphDrPrescription/default/getSignatureByPin*').as('signatureRequest');
            cy.getBySel('signatory-list-table').within($table => {
                cy.get('thead tr').should('not.contain', 'Date');
                cy.getBySel('signature-control-widget').should('have.length', 5);

                cy.contains(this.test_sign_role)
                    .closest('tr')
                    .findBySel('signature-control-widget')
                    .within($widget => {
                        cy.get('input').type(this.loggedInUser.body.pincode);
                        cy.get('button').click();
                    });
            });

            cy.wait('@signatureRequest');
            cy.getBySel('signatory-list-table').within($table => {
                cy.get('thead tr').should('contain', 'Date');

                cy.contains(this.test_sign_role)
                    .closest('tr')
                    .within($tr => {
                        cy.contains(this.seederData.logged_in_user_name);
                        cy.getBySel('signature-wrapper').should('be.visible');
                    });
            });

            itDisplaysSignatoriesInOrder(this.signatory_names);

            cy.intercept('OphDrPrescription/default/removeSignature*').as('removeSignatureRequest');
            itRemovesSignatureByRole(cy.getBySel('signatory-list-table'), this.test_sign_role);
            cy.wait('@removeSignatureRequest');

            cy.contains(this.test_sign_role)
                .closest('tr')
                .within($tr => {
                    cy.getBySel('signature-control-widget').should('be.visible');
                });
        });
    });

    // this test is skipped for now as the dispense location and condition is broken in OpenEyes
    context('for different institutions show all + local signatories', () => {
        before(function() {
            cy.wrap(["Prescriber", "Evaluated by", "Implemented by", "Confirmed by", "Guided by"]).as('signatory_names_DEM4');
            cy.wrap("Guided by").as("test_sign_role_DEM4");

            cy.login()
                .then(() => {
                    cy.setSystemSettingValue(REQUIRE_PIN_SIGN_SETTING, '0');

                    return cy.runSeeder('OphDrPrescription', 'PrescriptionPINSecondarySignSeeder', {
                        "secondary_signatories": this.signatory_names_DEM4.slice(1), //skip Prescriber
                        "delete_existing": false,
                        "institution_remote_id": "DEM4"
                    });

                })
                .as('seederData');

            cy.createModels(
                'OphDrPrescription_DispenseCondition',
                [],
                {
                    'name': "Holby to supply",
                    'display_order': 0
                }
            ).then(attributes => {
                cy.createModels(
                    'OphDrPrescription_DispenseCondition_Institution',
                    [],
                    {
                        'dispense_condition_id': attributes.id,
                        'institution_id': this.seederData.institution_id
                    }
                ).then(attributes => cy.wrap(attributes).as('dispenseConditionInstitutionAttributes'));
            });

            cy.createModels(
                'OphDrPrescription_DispenseLocation',
                [],
                {
                    'name': "Holby's Pharmacy",
                    'display_order': 9
                }
            ).then(attributes => {
                cy.createModels(
                    'OphDrPrescription_DispenseLocation_Institution',
                    [],
                    {
                        'dispense_location_id': attributes.id,
                        'institution_id': this.seederData.institution_id
                    }
                ).then(attributes => {
                    cy.createModels(
                        'OphDrPrescription_DispenseCondition_Assignment',
                        [],
                        {
                            'dispense_condition_institution_id': this.dispenseConditionInstitutionAttributes.id,
                            'dispense_location_institution_id': attributes.id
                        }
                    );
                });
            });

            cy.createPatient()
                .then(patient => {
                    cy.getEventCreationUrl(patient.id, 'OphDrPrescription')
                        .then((url) => {
                            cy.wrap(url).as('eventCreateUrl');
                        });
                });
        });

        beforeEach(function () {
            cy.login(undefined, undefined, 4, this.seederData.institution_id);
        });

        it('displays the correct list of signatories for institution', function() {
            cy.visit(this.eventCreateUrl);
            fillPrescriptionEventForm();

            cy.getBySel('signatory-list-table').find('tbody').within(tbody => {
                cy.get('tr').should('have.length', 1);
                cy.contains('Prescriber');
                cy.getBySel('signature-wrapper').should('be.visible');
            });

            cy.getBySel('event-action-save').first().click();
            cy.url().as('eventViewUrl', { type: 'static' });

            const ultimate_signatory_list = [];

            this.signatory_names_DEM4.slice(1).forEach((element, index) => {
                ultimate_signatory_list.push(this.signatory_names[++index]);
                ultimate_signatory_list.push(element);
            });
            ultimate_signatory_list.unshift(this.signatory_names_DEM4[0]);

            itDisplaysSignatoriesInOrder(ultimate_signatory_list);
        });
    });
});
