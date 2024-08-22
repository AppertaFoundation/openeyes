// Covers functionality introduced by OE-13040 - templates for operation notes
describe('test operation note template functionality', () => {
    before(() => {
        cy.login();
    });

    describe('create an operation note template and test its functionality', () => {
        beforeEach(() => {
            cy.login();
        });

        it('creates an operation note from a fixture, then saves it as a template', () => {
            cy.createPatient()
                .its('id')
                .then((patientId) => {
                    cy.getEventCreationUrl(patientId, 'OphTrOperationnote')
                        .then((url) => {
                            cy.visit(url);
                        });
                });

            cy.contains('Create default op note').click();
            cy.fixture('13040-operation-note-templates')
                .then((fixture) => {
                    cy.fillOperationNote(fixture.templateData).then(() => {
                        cy.getBySel('event-action-save').first().click();

                        cy.getBySel('save-new-template').click();
                        cy.getBySel('template-name').type(fixture.templateData.name);

                        cy.intercept('/OphTrOperationnote/Default/saveTemplate').as('saveTemplate');
                        cy.getBySel('save-template').click();
                        // ensure template is saved before test completes
                        cy.waitFor('@saveTemplate');
                        // TODO: check for success message?
                    });
                });
        });

        it('creates an operation booking with the same procedure set as the original operation note, creates an operation note from said booking, and verifies that data is prefilled correctly from fixture', () => {
            cy.runSeeder('OphInBiometry', 'PopulatedBiometrySeeder')
                .then((body) => {
                    return body.event.patient_id;
                })
                .as('patientId');

            cy.fixture('13040-operation-note-templates')
                .then((fixture) => {
                    cy.createModels(
                        'OphTrOperationnote_Template',
                        fixture.templateData.definition.states,
                        fixture.templateData.definition.attributes
                    ).as('opNoteTemplate');

                    let procedureNames = Object.entries(fixture.templateData.elementData.procedures).map(proc => proc[0]);
                    cy.get('@patientId').then((patientId) => {
                        cy.runSeeder('OphTrOperationbooking', 'OpBookingWithProceduresSeeder', {procedure_names: procedureNames, patient_id: patientId})
                            .then(() => {
                                return cy.getEventCreationUrl(patientId, 'OphTrOperationnote');
                            })
                            .then((url) => {
                                cy.visit(url);
                                cy.get('@opNoteTemplate').then((opNoteTemplate) => {
                                    cy.getBySel('template-entry').contains(opNoteTemplate.event_template.name).click();
                                    cy.verifyOperationNoteData(fixture.templateData);
                                });
                            });
                    });
                });
        });

        it('creates an operation note without a booking and verifies that the template does not appear', () => {
            cy.createPatient().then((patient) => {
                cy.getEventCreationUrl(patient.id, 'OphTrOperationnote')
                    .then((url) => {
                        cy.visit(url);
                    });

                cy.getBySel('template-entry').should('not.exist');
            });
        });
    });

    describe('create an operation note template and ensure that complications are included according to the "Allow saving of complications in Op Note templates" system setting', () => {
        const COMPLICATION_SETTING = 'allow_complications_in_pre_fill_templates';
        beforeEach(() => {
            cy.login();
        });

        it('sets the "Allow saving of complications in Op Note templates" system setting to false and ensures no complications are included', () => {
            cy.setSystemSettingValue(COMPLICATION_SETTING, 'off');

            cy.createPatient().its('id').as('patientId');

            cy.fixture('13040-operation-note-templates')
                .then((fixture) => {
                    let procedureNames = Object.entries(fixture.templateData.elementData.procedures).map(proc => proc[0]);
                    cy.get('@patientId').then((patientId) => {
                        cy.runSeeder('OphTrOperationbooking', 'OpBookingWithProceduresSeeder', {procedure_names: procedureNames, patient_id: patientId})
                            .then((body) => {
                                return body.event.procedureSetId;
                            })
                            .as('procedureSetId');

                        cy.getEventCreationUrl(patientId, 'OphTrOperationnote')
                            .then((url) => {
                                cy.visit(url);
                            }).then(() => {
                                cy.getBySel('template-entry').contains(fixture.templateData.name).click();
                            }).then(() => {
                                cy.getBySel(Object.entries(fixture.templateData.elementData.procedures)[0][1].values.complications.testid).parent().parent().find('ul > li').should('not.exist');
                            });
                    });
                });
        });

        it('sets the "Allow saving of complications in Op Note templates" system setting to true and ensures complications are included', () => {
            cy.setSystemSettingValue(COMPLICATION_SETTING, 'on');

            cy.createPatient()
                .its('id')
                .as('patientId')
                .then((patientId) => {
                    cy.getEventCreationUrl(patientId, 'OphTrOperationnote')
                        .then((url) => {
                            cy.visit(url);
                        });
                });

            cy.contains('Create default op note').click();

            cy.fixture('13040-operation-note-templates')
                .then((fixture) => {
                    let templateName = fixture.templateData.name + " with complications";

                    cy.fillOperationNote(fixture.templateData);

                    cy.getBySel('event-action-save').first().click();

                    cy.getBySel('save-new-template').click();
                    cy.getBySel('template-name').type(templateName);

                    cy.getBySel('save-template').click();

                    let procedureNames = Object.entries(fixture.templateData.elementData.procedures).map(proc => proc[0]);
                    cy.get('@patientId').then((patientId) => {
                        cy.runSeeder('OphTrOperationbooking', 'OpBookingWithProceduresSeeder', {procedure_names: procedureNames, patient_id: patientId})
                            .then((body) => {
                                return body.event.procedureSetId;
                            })
                            .as('procedureSetId');

                        cy.getEventCreationUrl(patientId, 'OphTrOperationnote')
                            .then((url) => {
                                cy.visit(url);
                            }).then(() => {
                                cy.getBySel('template-entry').contains(templateName).click();
                            }).then(() => {
                                cy.getBySel(Object.entries(fixture.templateData.elementData.procedures)[0][1].values.complications.testid).parent().parent().find('ul li').should('exist');
                            });
                    });
                });
        });

        after(() => {
            cy.resetSystemSettingValue(COMPLICATION_SETTING);
        });
    });

    describe('verify that user profile template page is functional', { testIsolation: false }, () => {
        beforeEach(() => {
            cy.clearLocalStorage()
            cy.clearCookies()

            cy.login();

            cy.getBySel("user-profile-link").click();
            cy.contains("Pre-fill templates").click();
        })

        it('renames an operation note template', () => {
            cy.getBySel("template-edit-button").first().click();
            cy.getBySel("template-name-field").first().clear().type("Edited template name");
            cy.getBySel("template-save-button").first().click();

            cy.getBySel("user-profile-link").click();
            cy.contains("Pre-fill templates").click();

            cy.getBySel("template-name-label").first().should("have.text", "Edited template name");
        });

        it('deletes operation note templates', () => {
            cy.getBySel("template-delete-button").each(($el) => {
                cy.wrap($el).click();

                cy.getBySel("alert-ok").click();
            });

            cy.getBySel("user-profile-link").click();
            cy.contains("Pre-fill templates").click();

            cy.getBySel("template-row").should("not.exist");
        });
    });
});