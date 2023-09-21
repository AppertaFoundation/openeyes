describe('verifies CVI Clinical Disorders admin page behaviour', function() {

    before(function() {
        cy.login()
            .then(function()  {
                return cy.runSeeder('OphCoCvi', 'ClinicalDisorderSeeder');
            }).as('seederData');
    });

    beforeEach(function() {
        cy.login();
    });

    function selectFilter(option) {
        cy.getBySel(`patient-type-dropdown`).select(option);
        cy.getBySel(`patient-type-filter-submit`).click();
    }

    it('ensures that the index page filter works', function() {

        cy.visit('/OphCoCvi/admin/clinicalDisorders');
        selectFilter('Diagnosis for patients 18 years of age or over');
        cy.url().should('include', 'patient_type');
        cy.getBySel(`clinical-disorder-result`).should('contain', this.seederData.ClinicalInfoDisorder.PATIENT_TYPE_ADULT.term_to_display);

        selectFilter('Diagnosis for patients under the age of 18');
        cy.url().should('include', 'patient_type');
        cy.getBySel(`clinical-disorder-result`).should('contain', this.seederData.ClinicalInfoDisorder.PATIENT_TYPE_CHILD.term_to_display);
    });

    it('should show/hide section options based on the selected patient type', function() {
        cy.visit('/OphCoCvi/admin/addClinicalDisorder');
        cy.getBySel(`patient-type-dropdown`).select('1');

        cy.getBySel('section-dropdown').each(($option) => {
            cy.wrap($option).then(($option) => {
                const patientType = $option.attr('data-patient-type');
                const isVisible = patientType === '1' || patientType === undefined;
                const optionText = $option.text().trim();
                cy.getBySel('section-dropdown').should(
                    isVisible ? 'contain' : 'not.contain',
                    optionText
                );
            });
        });
    });

    it('adds new CVI Clinical Disorders on the admin page', function() {

        cy.visit('/OphCoCvi/admin/addClinicalDisorder');

        const code = `H` + new Date().toLocaleTimeString();
        const name = `Test add Clinical Disorder under the age of 18 ${code}`;
        cy.getBySel(`patient-type-dropdown`).select('Diagnosis for patients under the age of 18');
        cy.getBySel(`name`).type(name);

        cy.getBySel(`icd-10-code`).type(code);

        let disorderName = '';
        let snomedCode = '';
        cy.getBySel(`disorder-autocomplete`).type('neo');
        cy.get(`ul.oe-autocomplete li:first`).click();
        cy.get(`.oe-multi-select.inline li:first`).should('exist').then(li => {
            // Regexp to get rid of the snomed code in pharentesis at the end of the string
            disorderName = li.text().replace(/\s*\(\d+\)\s*/g, '');
            snomedCode = li.find('#disorder_id').val();
        });

        let sectionName = '';
        cy.getBySel(`section-dropdown`)
            .select(2);

        cy.getBySel(`section-dropdown`).find('option:selected')
            .then((option) => {
                sectionName = option.text();
            });

        cy.get('#et_save').click();

        selectFilter('Diagnosis for patients under the age of 18');

        cy.contains('table.standard tbody tr', name)
            .scrollIntoView()
            .should('be.visible')
            .within(() => {
                cy.get('td').eq(0).should('have.text', name);
                cy.get('td').eq(1).should('have.text', code);
                cy.get('td').eq(2).should('have.text', sectionName);
                cy.get('td').eq(3).should('have.text', disorderName);
                cy.get('td').eq(4).should('have.text', snomedCode);
            });
    });

    it('should edit an existing CVI Clinical Disorder', function() {
        cy.visit('/OphCoCvi/admin/clinicalDisorders');
        cy.getBySel('clinical-disorder-result').find('tr.clickable:first').click();
        cy.getBySel('patient-type-dropdown').select("Diagnosis for patients under the age of 18");

        let name = '';
        let code = '';
        cy.getBySel('name').then(input => {
            name = `TEST-EDIT - ${input.val()}`;
            cy.getBySel('name').type('{selectall}').type(name);

            // After setting the name, continue with other actions inside this .then() block
            cy.getBySel('icd-10-code').then(input => {
                code = `TEST-EDIT - ${input.val()}`;
                cy.getBySel('icd-10-code').type('{selectall}').type(code);

                // After setting the code, continue with other actions inside this .then() block
                cy.get('.oe-multi-select.inline .remove-circle').click();
                cy.get('.oe-multi-select.inline li:first').should('not.exist');

                let disorderName = '';
                let snomedCode = '';
                cy.getBySel('disorder-autocomplete').type('nerve');
                cy.get('ul.oe-autocomplete li:first').click();
                cy.get('.oe-multi-select.inline li:first').should('exist').then(li => {
                    // Regexp to get rid of the snomed code in parentheses at the end of the string
                    disorderName = li.text().replace(/\s*\(\d+\)\s*/g, '');
                    snomedCode = li.find('#disorder_id').val();

                    // After setting disorderName and snomedCode, continue with other actions inside this .then() block
                    let sectionName = '';
                    cy.getBySel('section-dropdown').select(3);
                    cy.getBySel('section-dropdown').find('option:selected').then((option) => {
                        sectionName = option.text();

                        // After setting sectionName, continue with other actions inside this .then() block
                        cy.get('#et_save').click();

                        selectFilter('Diagnosis for patients under the age of 18');

                        cy.contains('table.standard tbody tr', name)
                            .scrollIntoView()
                            .should('be.visible')
                            .within(() => {
                                cy.get('td').eq(0).should('have.text', name);
                                cy.get('td').eq(1).should('have.text', code);
                                cy.get('td').eq(2).should('have.text', sectionName);
                                cy.get('td').eq(3).should('have.text', disorderName);
                                cy.get('td').eq(4).should('have.text', snomedCode);
                            });
                    });
                });
            });
        });
    });
});
