describe('systemic disorder widget behaviour', () => {

    // Date added to allow multiple runs in same environment without conflict
    let expectedDisorderTerm1 = 'Expected One ' + Date.now();
    let expectedDisorderTerm2 = 'Expected Two ' + Date.now();
    let unexpectedDisorderTerm = 'UnExpected ' + Date.now();

    before(() => {
        cy.login()
            .then((context) => {
                cy.createModels('CommonSystemicDisorder', [
                        ['forInstitution', context.body.institution_id], 
                        ['forDisplayOrder', 2], 
                        ['forKnownDisorderTerm', expectedDisorderTerm1]
                    ]);
                cy.createModels('CommonSystemicDisorder', [
                        ['forInstitution', context.body.institution_id], 
                        ['forDisplayOrder', 1],
                        ['forKnownDisorderTerm', expectedDisorderTerm2]
                    ]);                
                cy.createModels('CommonSystemicDisorder', [
                        ['forKnownDisorderTerm', unexpectedDisorderTerm]
                    ]);

                return cy.createPatient();
            })
            .then((patient) => {
                return cy.getEventCreationUrl(patient.id, 'OphCiExamination')
                    .then((url) => {
                        return [url, patient];
                    });
            })
            .then(([url, patient]) => {
                return cy.visit(url);
                // this element is already on screen
                // return cy.addExaminationElement('Systemic Diagnoses');
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

    it('only loads common systemic disorders mapped to the current institution', function () {
        cy.getBySel('add-systemic-diagnoses-button').click();

        cy.getBySel('systemic-diagnoses-popup')
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

    it('displays common systemic disorders in the correct display order', function () {
        cy.getBySel('systemic-diagnoses-popup')
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