describe('ophthalmic disorder group behaviour', { testIsolation: false }, () => {

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
                cy.createModels('CommonSystemicDisorderGroup', [
                        ['withInstitution', context.body.institution_id],
                        ['forDisplayOrder', 2]
                    ], 
                    {
                        name: groupName1
                    });
                cy.createModels('CommonSystemicDisorderGroup', [
                        ['withInstitution', context.body.institution_id],
                        ['forDisplayOrder', 1]
                    ], 
                    {
                        name: groupName2
                    });    
                cy.createModels('CommonSystemicDisorderGroup', [], 
                    {
                        name: unexectedGroupName
                    });  
                
                
                cy.createModels('CommonSystemicDisorder', [
                        ['withInstitution', context.body.institution_id],
                        ['forKnownDisorderTerm', group1DisorderTerm],
                        ['forKnownGroupName', groupName1]
                    ]);
                cy.createModels('CommonSystemicDisorder', [
                        ['withInstitution', context.body.institution_id],
                        ['forKnownDisorderTerm', group2DisorderTerm],
                        ['forKnownGroupName', groupName2]
                    ]);                
                cy.createModels('CommonSystemicDisorder', [
                        ['withInstitution', context.body.institution_id],
                        ['forKnownDisorderTerm', noGroupDisorderTerm]
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
            });
    });

    beforeEach(() => {
        cy.clearLocalStorage();
        cy.clearCookies();
        cy.getModelByAttributes('Disorder', {term: group1DisorderTerm})
            .as('group1Disorder');
        cy.getModelByAttributes('Disorder', {term: group2DisorderTerm})
            .as('group2Disorder');
        cy.getModelByAttributes('Disorder', {term: noGroupDisorderTerm})
            .as('noGroupDisorder');

        cy.getModelByAttributes('CommonSystemicDisorderGroup', {name: groupName1})
            .as('group1');
        cy.getModelByAttributes('CommonSystemicDisorderGroup', {name: groupName2})
            .as('group2');
        cy.getModelByAttributes('CommonSystemicDisorderGroup', {name: unexectedGroupName})
            .as('unexpectedGroup');
        cy.login();
    });

    it('only loads common systemic disorder groups mapped to the current institution', function () {

        cy.removeElements([], true);
        cy.addExaminationElement('Systemic Diagnoses');

        cy.getBySel('add-systemic-diagnoses-button').click();
        cy.getBySel('systemic-diagnoses-popup')
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

    it('displays common systemic disorder groups in the correct display order', function () {
        cy.getBySel('systemic-diagnoses-popup')
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

    it('common systemic disorder groups filters disorders correctly', function () {
        cy.getBySel('systemic-diagnoses-popup')
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