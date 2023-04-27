describe('ophthalmic disorder group behaviour', () => {
    let seederData;

    before(() => {
        cy.login()
            .then((context) => {
                return cy.runSeeder('OphCiExamination', 'CommonOphthalmicDisorderGroupSeeder');
            })
            .then((data) => {
                return cy.getEventCreationUrl(data.patient.id, 'OphCiExamination')
                    .then((url) => {
                        data.createUrl = url;
                        return data;
                    });
                })
            .then(function (data) {
                seederData = data;
            });
    });

    beforeEach(() => {
        cy.login()
            .then(() => {
                cy.visit(seederData.createUrl);
                cy.addExaminationElement('Ophthalmic Diagnoses');
            })
    });

    it('only loads common ophthalmic disorder groups mapped to the current institution', function () {
        cy.getBySel('add-ophthalmic-diagnoses-button').click();
        cy.getBySel('ophthalmic-diagnoses-popup')
            .should('be.visible')
            .within(() => {
                seederData.expected_groups.forEach((expectedGroup) => {
                    cy.get(`li[data-filter-value="${expectedGroup.group.id}"]`)
                    .should('exist')
                    .scrollIntoView()
                    .should('be.visible');
                });
                seederData.unexpected_groups.forEach((unexpectedGroup) => {
                    cy.get(`li[data-filter-value="${unexpectedGroup.id}"]`)
                        .should('not.exist');
                });
            });
    });

    it('displays common ophthalmic disorder groups in the correct display order', function () {
        cy.getBySel('add-ophthalmic-diagnoses-button').click();
        cy.getBySel('ophthalmic-diagnoses-popup')
            .should('be.visible')
            .within(() => {
                cy.get('ul[data-id="disorder-group-filter"]')
                    .should('be.visible')
                    .within(() => {
                        let disorderGroup0index, disorderGroup1index;

                        cy.get('li')
                            .should('be.visible')
                            .each((element, index) => {
                                if (parseInt(element.data('filter-value')) === parseInt(seederData.expected_groups[0].group.id)) {
                                    disorderGroup0index = index;
                                }
                                if (parseInt(element.data('filter-value')) === parseInt(seederData.expected_groups[1].group.id)) {
                                    disorderGroup1index = index;
                                }
                            })
                            .then(() => {
                                expect(disorderGroup0index).to.be.greaterThan(disorderGroup1index);
                            });
                    });
            });
    });

    it('common ophthalmic disorder groups filters disorders correctly', function () {
        cy.getBySel('add-ophthalmic-diagnoses-button').click();
        cy.getBySel('ophthalmic-diagnoses-popup')
            .should('be.visible')
            .within(() => {
                seederData.expected_groups.forEach((expectedGroup) => {
                    cy.get(`li[data-filter-value="${expectedGroup.group.id}"]`)
                    .should('exist')
                    .scrollIntoView()
                    .click()
                    .then(() => {
                        let expectedIds = expectedGroup.disorders.map((disorder) => {
                            return parseInt(disorder.disorder_id);
                        });
                        cy.get('ul[data-id="disorder-list"]')
                            .within(() => {
                                cy.get('li')
                                    .each((element) => {
                                        if (expectedIds.includes(parseInt(element.data('id')))) {
                                            cy.wrap(element)
                                                .scrollIntoView()
                                                .should('be.visible');
                                        } else {
                                            cy.wrap(element)
                                                .scrollIntoView()
                                                .should('not.be.visible');
                                        }
                                    });
                            });
                        })
                    });
            });
    });
});