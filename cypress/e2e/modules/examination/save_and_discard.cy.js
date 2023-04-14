describe('the save and discard functionality is controlled by a setting and allows the user to automatically discard elements from an examination that they have not interacted with', () => {
    
    const SAVE_AND_DISCARD_SETTING = 'close_incomplete_exam_elements';

    // login as admin and create a patient
    beforeEach(() => {
        cy.login()
            .then(() => {
                return cy.createPatient();
            })
            .as('patient');
    });

    describe('check the behaviour works when the setting is enabled', () => {

        // select "on" for setting "Offer to automatically close incomplete examination elements"
        beforeEach(() => {
            cy.setSystemSettingValue(SAVE_AND_DISCARD_SETTING, 'on');
        });

        const elementsForTesting = [
            [['History'], ['Ophthalmic Diagnoses', 'PCR Risk']]
        ];

        const elementFillers = {
            'History': () => {
                cy.getElementByName('History').within(() => {
                    cy.getBySel('history-description').type('foobar');
                });
            }
        };

        const assertElementsAreInDiscardList = (elementsToDiscard) => {
            cy.getBySel('dialog-confirm').within(() => {
                elementsToDiscard.forEach((expectedElement) => {
                    cy.getBySel('element-discard-list').contains(expectedElement);
                });
            });
        };

        it('will offer to discard the untouched elements', function () {

            cy.visitEventCreationUrl(this.patient.id, 'OphCiExamination')
                .then(() => {
                    elementsForTesting.forEach(([elementsToTouch, elementsToDiscard]) => {
                        cy.removeElements();
                        cy.addExaminationElement(elementsToTouch.concat(elementsToDiscard));

                        elementsToTouch.forEach((elementToTouch) => {
                            elementFillers[elementToTouch]();
                        });

                        cy.saveEvent()
                            .then(() => assertElementsAreInDiscardList(elementsToDiscard));
                    });
                })

        });

        it('ensures that an event is saved when save and discard is switched on AND there is only one element', function () {
            
            // add an examination event for our patient
            cy.visitEventCreationUrl(this.patient.id, 'OphCiExamination')
                .then(() => { 
                    // remove all elements
                    cy.removeElements();             
                    // enter only history notes
                    cy.addExaminationElement('History');
                    elementFillers['History']();
                })

            // assert that the event has been saved with just one element
            cy.saveEvent()
                .then(() => {
                     cy.assertEventSaved();
                 });

            // assert that the history notes are as previously entered
            cy.getBySel('view-history-comments').contains('foobar');

        });

    });

});