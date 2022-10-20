let currentInstitutionId, currentSubSpecialityId;

describe('ophthalmic diagnoses widget behaviour', () => {
    before(() => {
        cy.login()
            .then((response) => {
                currentInstitutionId = response.body.institution_id;
                currentSubSpecialityId = response.body.subspeciality_id;

                return [cy.createPatient(), response.body.institution_id, response.body.subspeciality_id];
            })
            .then(([patient, institution_id, subspeciality_id]) => {
                cy.createModels('CommonOphthalmicDisorder', [['forInstitution', institution_id]], {subspeciality_id: subspeciality_id})
                    .as('expectedCommonOphthalmicDisorder1');
                cy.createModels('CommonOphthalmicDisorder', [['forInstitution', institution_id]], {subspeciality_id: subspeciality_id})
                    .as('expectedCommonOphthalmicDisorder2');
                
                cy.createModels('CommonOphthalmicDisorder', [], {subspeciality_id: subspeciality_id})
                    .as('unexpectedCommonOphthalmicDisorder');

                return patient;
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

    it('only loads diagnoses mapped to the current institution', () =>{
        console.log

        cy.getBySel('add-ophthalmic-diagnoses-button').click();

        cy.getBySel('ophthalmic-diagnoses-popup')
            .should('be.visible');


    });
});