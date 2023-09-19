describe('local patient update tests', () => {

    const DEFAULT_COUNTRY_SETTING = 'default_country';
    beforeEach(() => {
        cy.login()
            .then(() => {
                cy.createPatient(['localPatient']).its('id').as('patientId');
            });
    });

    describe('Australia add referring practitioner tests', () => {
        before(() => {
            cy.setSystemSettingValue(DEFAULT_COUNTRY_SETTING, 'Australia');
        });

        it('able to add referring practitioner with email for existing practice with email and also test email validation', () => {
            cy.get('@patientId').then((patientId) => {
                cy.visit(`/patient/update/${patientId}?prevUrl=%2Fpatient%2Fsummary%2F${patientId}`).then(() => {
                    cy.getBySel('add-referring-practitioner-australia').click();
                    cy.intercept('gp/create?context=AJAX').as('ajaxGpCreate');

                    cy.get('#extra_gp_adding_form').within(() => {

                        cy.getBySel("gp-form-first-name").find('input').type('firstname');
                        cy.getBySel("gp-form-last-name").find('input').type('lastname');
                        cy.getBySel("gp-form-email").find('input').type('email');
                        cy.getBySel('gp-adding-form-button-next').click();

                        cy.wait('@ajaxGpCreate');

                        cy.getBySel('gp-create-form-errors')
                            .should('contain', 'Email is not a valid email address.');
                        cy.getBySel("gp-form-email").find('input').clear().type('email@test.com');
                        cy.getBySel('gp-adding-form-button-next').click();

                        cy.wait('@ajaxGpCreate');


                    }).then(() => {
                        cy.intercept('POST', 'practiceAssociate/create').as('practiceCreate');
                        cy.getBySel('extra-practice-form').within(() => {

                            cy.get('#autocomplete_extra_practice_id').type('Jenny');
                            cy.get('.oe-autocomplete').first().click();
                            cy.getBySel('add-practice-form-add').click();
                            cy.wait('@practiceCreate');

                        }).then(() => {
                            cy.getBySel('save-patient').click();
                            cy.get('.js-demographics-btn').click();
                            cy.getBySel('pas-contacts-gp-email').contains('email@test.com');
                        });
                    });
                });

            });
        });

        it('able to add referring practitioner with email for new practice with email and also test email validation', () => {
            cy.get('@patientId').then((patientId) => {
                cy.visit(`/patient/update/${patientId}?prevUrl=%2Fpatient%2Fsummary%2F${patientId}`).then(() => {
                    cy.getBySel('add-referring-practitioner-australia').click();
                    cy.intercept('gp/create?context=AJAX').as('ajaxGpCreate');

                    cy.generateRandomString(6).then((randomString) => {
                        const email = randomString + "@test.com";
                        cy.get('#extra_gp_adding_form').within(() => {


                            cy.getBySel("gp-form-first-name").find('input').type(randomString);
                            cy.getBySel("gp-form-last-name").find('input').type(randomString);
                            cy.getBySel("gp-form-email").find('input').clear().type(email);

                            cy.getBySel('gp-adding-form-button-next').click();

                            cy.wait('@ajaxGpCreate');
                        }).then(() => {
                            cy.getBySel('extra-practice-form').within(() => {
                                cy.getBySel('add-practice').click();
                            }).then(() => {

                                cy.intercept('POST', 'practice/createAssociate').as('practiceCreate');
                                cy.get('#extra-adding-practice-form').within(() => {
                                    cy.getBySel("add-practice-name").find('textarea').type(randomString);
                                    cy.getBySel("add-practice-phone").find('input').type('22222222');
                                    cy.getBySel("form-address-one").find('input').type(randomString);
                                    cy.getBySel("form-address-two").find('input').type(randomString);
                                    cy.getBySel("form-address-city").find('input').type(randomString);
                                    cy.getBySel("form-address-postcode").find('input').type(randomString);

                                    cy.getBySel('add-new-practice').click();
                                    cy.wait("@practiceCreate");
                                });

                                cy.getBySel('save-patient').click();
                                cy.get('.js-demographics-btn').click();
                                cy.getBySel('pas-contacts-gp-email').contains(email);
                            });
                        });
                    });
                });
            });
        });

        after(() => {
            cy.resetSystemSettingValue(DEFAULT_COUNTRY_SETTING);
        });
    });
});