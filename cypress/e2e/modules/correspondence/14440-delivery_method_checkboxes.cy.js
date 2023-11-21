describe('Appropriate checkboxes should get checked for delivery methods', () => {
    describe('Print checkbox should get checked when email(delayed) and email(immediately) parameters are off', () => {
        before(() => {
            cy.login()
            .then(() => {
                //Make sure "send_email_delayed" & "send_email_immediately" setting is OFF
                cy.setSystemSettingValue('send_email_delayed', 'off');
                cy.setSystemSettingValue('send_email_immediately', 'off');  
                return cy.createPatient();
            }).as('patient');        
        })
        it('Test that the delivery method "Print" is checked for the patient recipient', function () {
            // Create examination event
            cy.get('@patient')
                .then((data) => {
                    cy.getEventCreationUrl(data.id, 'OphCiExamination')
                        .then((url) => {
                            cy.visit(url);
                            // Remove all examination event elements
                            cy.removeElements([], true);             
                            // Add a communication preferences element
                            cy.addExaminationElement('Communication Preferences');
                            //Click on checkbox "agrees to insecure email correspondence"
                            cy.getBySel('agrees_to_insecure_email_correspondence').check();
                            cy.getBySel('event-action-confirm-and-save').first().click();

                            //Add correspondence event and test the recipient(patient) has a print option checked
                            cy.getEventCreationUrl(data.id, 'OphCoCorrespondence')
                                .then((url) => {
                                    cy.visit(url);
                                    //check recipient(patient) exists & print checkbox is checked
                                    cy.getBySel('dm_table').find('[data-test="docman_contact_type"]').find("option:selected")
                                    .contains('Patient').parents('tr').find('.docman_delivery_method').as('button')
                                    //Assert the print button should be checked and the email should not be existed
                                    cy.get('@button').find('input[type="checkbox"]')
                                    .should('have.value', 'Print').should('be.checked');
                                    cy.get('@button').find('[value="Email"]')
                                    .should('not.exist');
                                
                            })
                        });
                        
                });
        });
    })   
    describe('When email(immediately) parameter is ON and email(delayed) is OFF, the email checkbox should be ticked', () => {
        before(() => {
            cy.login()
            .then(() => {
                //Make sure "send_email_delayed" setting is OFF & "send_email_immediately" is ON
                cy.setSystemSettingValue('send_email_delayed', 'off');
                cy.setSystemSettingValue('send_email_immediately', 'on');  
                return cy.createPatient();
            }).as('patient');        
        }) 
        it('Test that the delivery method "email" is checked when the "Send Emails Immediately" setting is ON', function () {
            cy.get('@patient')
                .then((data) => {
                    cy.getEventCreationUrl(data.id, 'OphCiExamination')
                        .then((url) => {
                            cy.visit(url);
                            // Remove all examination event elements
                            cy.removeElements([], true);             
                            // Add a communication preferences element
                            cy.addExaminationElement('Communication Preferences');
                            //Click on checkbox "agrees to insecure email correspondence in examination event"
                            cy.getBySel('agrees_to_insecure_email_correspondence').check();
                            cy.getBySel('event-action-confirm-and-save').first().click();
                            
                            //Add correspondence event and test the recipient(patient) has a print option checked
                            cy.getEventCreationUrl(data.id, 'OphCoCorrespondence')
                                .then((url) => {
                                    cy.visit(url);
                                    cy.getBySel('dm_table').find('[data-test="docman_contact_type"]').find("option:selected")
                                    .contains('Patient').parents('tr')
                                    .find('[data-test="docman_delivery_method"]').as('Buttton')
                                    //Assert the email button to be checked and the print button to be unchecked
                                    cy.get('@Buttton').find('[value="Email"]')
                                    .should('be.checked');
                                    cy.get('@Buttton').find('[value="Print"]')
                                    .should('not.be.checked');
                                })
                            })
                        })                
        })
    })
    describe('Email(delayed) checkbox should be checked for delivery methods' , () => {
        before(() => {
            cy.login()
            .then(() => {
                //Make sure "send_email_delayed" setting is ON & "send_email_immediately" is OFF
                cy.setSystemSettingValue('send_email_delayed', 'on');
                cy.setSystemSettingValue('send_email_immediately', 'off');  
                return cy.createPatient();
            }).as('patient');        
        })
        it('Test that the delivery method "email-delayed" is checked when the "Send Emails Delayed" setting is ON', function () {
            cy.get('@patient')
                .then((data) => {
                    cy.getEventCreationUrl(data.id, 'OphCiExamination')
                        .then((url) => {
                            cy.visit(url);
                            // Remove all examination event elements
                            cy.removeElements([], true);             
                            // Add a communication preferences element
                            cy.addExaminationElement('Communication Preferences');
                            //Click on checkbox "agrees to insecure email correspondence in examination event"
                            cy.getBySel('agrees_to_insecure_email_correspondence').check();
                            cy.getBySel('event-action-confirm-and-save').first().click();
                            
                            //Add correspondence event and test the recipient(patient) has a print option checked
                            cy.getEventCreationUrl(data.id, 'OphCoCorrespondence')
                                .then((url) => {
                                    cy.visit(url);
                                    cy.getBySel('dm_table').find('[data-test="docman_contact_type"]').find("option:selected")
                                    .contains('Patient').parents('tr')
                                    .find('[data-test="docman_delivery_method"]').as('Buttton')
                                    //Assert that Email-delayed checkbox should be checked
                                    //And other buttons are unchecked
                                    cy.get('@Buttton').find('[value="Email (Delayed)"]')
                                    .should('be.checked');
                                    cy.get('@Buttton').find('[value="Print"]')
                                    .should('not.be.checked');
                                })
                        })
                })    
        })
    })
    describe('Electronic checkbox should be checked & disabled for GP' , () => {
        before(() => {
            cy.login()
            .then(() => {
                //Make sure "gp_label" setting is set to "GP" in system settings
                cy.setSystemSettingValue('gp_label', 'GP');
                return cy.createPatient();
            }).as('patient');
        })
            it('Test that the "electronic" delivery method is checked and disabled for GP recipient', function () {
                // Create examination event
                cy.get('@patient')
                    .then((data) => {
                        //Add correspondence event and test for recipient (gp), electronic checkbox is ticked and disabled
                        cy.getEventCreationUrl(data.id, 'OphCoCorrespondence')
                        .then((url) => {
                            cy.visit(url);
                            //check recipient (gp) exists & electronic checkbox is checked by default
                            cy.getBySel('dm_table').find('input[value="GP"]').parents('tr')
                            .find('[data-test="docman_delivery_method"]').as('Buttton')
                            //Assert the electronic button to be checked
                            cy.get('@Buttton').find('[value="Docman"]')
                            .should('be.checked','disabled');
                            //Assert print button to be unchecked
                            cy.get('@Buttton').find('[value="Print"]')
                            .should('not.be.checked');
                            //Assert email delayed button to be unchecked
                            cy.get('@Buttton').find('[value="Email (Delayed)"]')
                            .should('not.be.checked');
                        })
                    })
            })
    })
})
