describe('the save and discard functionality is controlled by a setting and allows the user to automatically discard elements from an examination that they have not interacted with', () => {
    const SAVE_AND_DISCARD_SETTING = 'close_incomplete_exam_elements';
    beforeEach(() => {
        cy.login()
            .then(() => {
                return cy.createPatient();
            })
            .as('patient');
    });

    describe('check the behaviour works when the setting is enabled', () => {
        beforeEach(() => {
            cy.setSystemSettingValue(SAVE_AND_DISCARD_SETTING, 'on');
        });

        const elementsForTesting = [
            // [['History'], ['Ophthalmic Diagnoses']],
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
    });

});