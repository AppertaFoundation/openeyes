describe('ophthalmic diagnoses widget behaviour', () => {
    before(() => {
        cy.login()
            .then((context) => {
                cy.createModels('CommonOphthalmicDisorder', [['forInstitution', context.body.institution_id], ['forDisplayOrder', 2]], {subspecialty_id: context.body.subspecialty_id})
                    .as('expectedCommonOphthalmicDisorder1');
                cy.createModels('CommonOphthalmicDisorder', [['forInstitution', context.body.institution_id], ['forDisplayOrder', 1]], {subspecialty_id: context.body.subspecialty_id})
                    .as('expectedCommonOphthalmicDisorder2');
                
                cy.createModels('CommonOphthalmicDisorder', [], {subspecialty_id: context.body.subspecialty_id})
                    .as('unexpectedCommonOphthalmicDisorder');

                return cy.createPatient();
            })
            .then((patient) => {
                return cy.getEventCreationUrl(patient.id, 'OphCiExamination')
                    .then((url) => {
                        return [url, patient];
                    });
            })
            .then(([url, patient]) => {
                cy.visit(url);
                return cy.addExaminationElement('Ophthalmic Diagnoses');
            });
    });

    it('only loads diagnoses mapped to the current institution', function () {
        console.log(['A', this.expectedCommonOphthalmicDisorder1]);

        cy.getBySel('add-ophthalmic-diagnoses-button').click();

        cy.getBySel('ophthalmic-diagnoses-popup')
            .should('be.visible')
            .within(() => {                  
                cy.get(`li[data-id="${this.expectedCommonOphthalmicDisorder1.disorder.id}"]`).should('exist');
                cy.get(`li[data-id="${this.expectedCommonOphthalmicDisorder1.disorder.id}"]`).scrollIntoView();                        
                cy.get(`li[data-id="${this.expectedCommonOphthalmicDisorder1.disorder.id}"]`).should('be.visible');

                cy.get(`li[data-id="${this.expectedCommonOphthalmicDisorder2.disorder.id}"]`).should('exist');
                cy.get(`li[data-id="${this.expectedCommonOphthalmicDisorder2.disorder.id}"]`).scrollIntoView();                        
                cy.get(`li[data-id="${this.expectedCommonOphthalmicDisorder2.disorder.id}"]`).should('be.visible');
                                
                cy.get(`li[data-id="${this.unexpectedCommonOphthalmicDisorder.disorder.id}"]`).should('not.exist');
            });        
    });

    it('displays to diagnoses in the correct display order', function () {
        console.log(['B', this.expectedCommonOphthalmicDisorder1]);

        cy.getBySel('ophthalmic-diagnoses-popup')
            .should('be.visible')
            .within(() => {    
                cy.get('ul[data-id="disorder-list"]')
                    .should('be.visible')
                    .within(() => {
                        let disorder1index, disorder2index;

                        cy.get('li').each(function (element, index) {
                            console.log(['compare', index, element.data('id')]);
                            if (element.data('id') === this.expectedCommonOphthalmicDisorder1.disorder.id) {
                                console.log('Match 1: ' + index);
                                disorder1index = index;
                            }
                            if (element.data('id') === this.expectedCommonOphthalmicDisorder2.disorder.id) {
                                console.log('Match 2: ' + index);
                                disorder2index = index;
                            }
                        });

                        expect(disorder1index).to.be.greaterThan(disorder2index)
                        // .then(() => {
                        //     console.log('comparing', disorder1index, disorder2index);    
                        //     expect(disorder1index).to.be.greaterThan(disorder2index)
                        // });
                    });
            });        
    });
});