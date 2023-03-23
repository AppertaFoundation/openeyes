describe('ophthalmic disorder widget behaviour', () => {

    // Date added to allow multiple runs in same environment without conflict
    let expectedDisorderTerm1 = 'Expected One ' + Date.now();
    let expectedDisorderTerm2 = 'Expected Two ' + Date.now();
    let unexpectedDisorderTerm = 'UnExpected ' + Date.now();

    before(() => {
        cy.login()
            .then((context) => {
                cy.createModels('CommonOphthalmicDisorder', [
                        ['withInstitution', context.body.institution_id], 
                        ['forDisplayOrder', 2], 
                        ['forKnownDisorderTerm', expectedDisorderTerm1]
                    ], 
                    {
                        subspecialty_id: context.body.subspecialty_id
                    });
                cy.createModels('CommonOphthalmicDisorder', [
                        ['withInstitution', context.body.institution_id], 
                        ['forDisplayOrder', 1],
                        ['forKnownDisorderTerm', expectedDisorderTerm2]
                    ], 
                    {
                        subspecialty_id: context.body.subspecialty_id
                    });                
                cy.createModels('CommonOphthalmicDisorder', [
                        ['forKnownDisorderTerm', unexpectedDisorderTerm]
                    ], 
                    {
                        subspecialty_id: context.body.subspecialty_id
                    });

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

    beforeEach(() => {
        cy.getModelByAttributes('Disorder', {term: expectedDisorderTerm1})
            .as('expectedDisorder1');
        cy.getModelByAttributes('Disorder', {term: expectedDisorderTerm2})
            .as('expectedDisorder2');
        cy.getModelByAttributes('Disorder', {term: unexpectedDisorderTerm})
            .as('unexpectedDisorder');
    });

    it('only loads common ophthalmic disorders mapped to the current institution', function () {
        cy.getBySel('add-ophthalmic-diagnoses-button').click();

        cy.getBySel('ophthalmic-diagnoses-popup')
            .should('be.visible')
            .within(() => {                  
                cy.get(`li[data-id="${this.expectedDisorder1.id}"]`)
                    .should('exist')
                    .scrollIntoView()
                    .should('be.visible');

                cy.get(`li[data-id="${this.expectedDisorder2.id}"]`)
                    .should('exist')
                    .scrollIntoView()
                    .should('be.visible');
                                
                cy.get(`li[data-id="${this.unexpectedDisorder.id}"]`)
                    .should('not.exist');
            });        
    });

    it('displays common ophthalmic disorders in the correct display order', function () {
        cy.getBySel('ophthalmic-diagnoses-popup')
            .should('be.visible')
            .within(() => {    
                cy.get('ul[data-id="disorder-list"]')
                    .should('be.visible')
                    .within(() => {
                        let disorder1index, disorder2index;

                        cy.get('li')
                            .should('be.visible')
                            .each((element, index) => {
                                if (element.data('id') === parseInt(this.expectedDisorder1.id)) {
                                    disorder1index = index;
                                }
                                if (element.data('id') === parseInt(this.expectedDisorder2.id)) {
                                    disorder2index = index;
                                }
                            })
                            .then(() => {
                                expect(disorder1index).to.be.greaterThan(disorder2index);
                            });
                    });
            });        
    });
});