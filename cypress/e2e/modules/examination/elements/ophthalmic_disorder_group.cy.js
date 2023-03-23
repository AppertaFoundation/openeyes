describe('ophthalmic disorder group behaviour', () => {

    // Date added to allow multiple runs in same environment without conflict
    let groupName1 = 'Group One ' + Date.now();
    let groupName2 = 'Group Two ' + Date.now();
    let unexectedGroupName = 'Unexpected Group ' + Date.now();

    let group1DisorderTerm = 'Group One Disorder ' + Date.now();
    let group2DisorderTerm = 'Group Two Disorder  ' + Date.now();
    let noGroupDisorderTerm = 'No Group Disorder ' + Date.now();

    before(() => {
        cy.login()
            .then((context) => {
                cy.createModels('CommonOphthalmicDisorderGroup', [
                        ['withInstitution', context.body.institution_id],
                        ['forDisplayOrder', 2]
                    ], 
                    {
                        name: groupName1
                    });
                cy.createModels('CommonOphthalmicDisorderGroup', [
                        ['withInstitution', context.body.institution_id],
                        ['forDisplayOrder', 1]
                    ], 
                    {
                        name: groupName2
                    });    
                cy.createModels('CommonOphthalmicDisorderGroup', [], 
                    {
                        name: unexectedGroupName
                    });  
                
                
                cy.createModels('CommonOphthalmicDisorder', [
                        ['withInstitution', context.body.institution_id],
                        ['forKnownDisorderTerm', group1DisorderTerm],
                        ['forKnownGroupName', groupName1]
                    ], 
                    {
                        subspecialty_id: context.body.subspecialty_id
                    });
                cy.createModels('CommonOphthalmicDisorder', [
                        ['withInstitution', context.body.institution_id],
                        ['forKnownDisorderTerm', group2DisorderTerm],
                        ['forKnownGroupName', groupName2]
                    ], 
                    {
                        subspecialty_id: context.body.subspecialty_id
                    });                
                cy.createModels('CommonOphthalmicDisorder', [
                        ['withInstitution', context.body.institution_id],
                        ['forKnownDisorderTerm', noGroupDisorderTerm]
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
        cy.getModelByAttributes('Disorder', {term: group1DisorderTerm})
            .as('group1Disorder');
        cy.getModelByAttributes('Disorder', {term: group2DisorderTerm})
            .as('group2Disorder');
        cy.getModelByAttributes('Disorder', {term: noGroupDisorderTerm})
            .as('noGroupDisorder');

        cy.getModelByAttributes('CommonOphthalmicDisorderGroup', {name: groupName1})
            .as('group1');
        cy.getModelByAttributes('CommonOphthalmicDisorderGroup', {name: groupName2})
            .as('group2');
        cy.getModelByAttributes('CommonOphthalmicDisorderGroup', {name: unexectedGroupName})
            .as('unexpectedGroup');
    });

    it('only loads common ophthalmic disorder groups mapped to the current institution', function () {
        cy.getBySel('add-ophthalmic-diagnoses-button').click();
        cy.getBySel('ophthalmic-diagnoses-popup')
            .should('be.visible')
            .within(() => {                  
                cy.get(`li[data-filter-value="${this.group1.id}"]`)
                    .should('exist')
                    .scrollIntoView()
                    .should('be.visible');

                cy.get(`li[data-filter-value="${this.group2.id}"]`)
                    .should('exist')
                    .scrollIntoView()
                    .should('be.visible');
                                
                cy.get(`li[data-filter-value="${this.unexpectedGroup.id}"]`)
                    .should('not.exist');
            });        
    });

    it('displays common ophthalmic disorder groups in the correct display order', function () {
        cy.getBySel('ophthalmic-diagnoses-popup')
            .should('be.visible')
            .within(() => {    
                cy.get('ul[data-id="disorder-group-filter"]')
                    .should('be.visible')
                    .within(() => {
                        let disorderGroup1index, disorderGroup2index;

                        cy.get('li')
                            .should('be.visible')
                            .each((element, index) => {
                                if (element.data('filter-value') === parseInt(this.group1.id)) {
                                    disorderGroup1index = index;
                                }
                                if (element.data('filter-value') === parseInt(this.group2.id)) {
                                    disorderGroup2index = index;
                                }
                            })
                            .then(() => {
                                expect(disorderGroup1index).to.be.greaterThan(disorderGroup2index);
                            });
                    });
            });        
    });

    it('common ophthalmic disorder groups filters disorders correctly', function () {
        cy.getBySel('ophthalmic-diagnoses-popup')
            .should('be.visible')
            .within(() => {    
                cy.get(`li[data-filter-value="${this.group1.id}"]`)
                    .should('exist')
                    .scrollIntoView()
                    .click({force : true})
                    .then(() => {
                        cy.get(`li[data-id="${this.group1Disorder.id}"]`)
                            .should('exist')
                            .scrollIntoView()
                            .should('be.visible');
                                        
                        cy.get(`li[data-id="${this.group2Disorder.id}"]`)
                            .should('exist')
                            .scrollIntoView()
                            .should('not.be.visible');  
                    });
            });        
    });
});