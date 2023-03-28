describe('visual fields images behaves correctly in both automated and manual setting', () => {
    beforeEach(() => {
        cy.login()
            .then((context) => {
                return cy.runSeeder(
                    'OphGeneric',
                    'VisualFieldsSeeder'
                ).then((seederResult) => {
                    return {
                        'seederResult': seederResult,
                        'context': context,
                        'eventViewUrl': '/OphGeneric/default/view/',
                        'eventEditUrl': '/OphGeneric/default/update/',
                    }
                })
            })
            .as('data');
    });

    it('automatically generated event shows correct elements', function () {
        cy.visit(this.data.eventViewUrl + '' + this.data.seederResult.automated_event.id)
            .then(() => {
                this.data.seederResult.automated_event.element_names.forEach((element_name) => {
                    cy.getByDataAttrContains('element-type-class', element_name).should('exist');
                });
            });
    });

    it('automatically generated event enables edit on correct elements', function () {
        cy.visit(this.data.eventEditUrl + '' + this.data.seederResult.automated_event.id)
            .then(() => {
                cy.getByDataAttrContains('element-type-class', 'Comments')
                    .should('exist')
                    .within(()=> {
                        cy.getBySel('comments-edit').should('exist');
                        cy.getBySel('comments-view').should('not.exist');
                    });

                cy.getByDataAttrContains('element-type-class', 'HFA')
                    .should('exist')
                    .within(()=> {
                        cy.getBySel('hfa-edit').should('not.exist');
                        cy.getBySel('hfa-view').should('exist');
                    });
            });
    });

    it('automatically generated event shows correct elements after save', function () {
        cy.visit(this.data.eventEditUrl + '' + this.data.seederResult.automated_event.id)
            .then(() => {
                cy.getByDataAttrContains('element-type-class', 'Comments')
                    .should("be.visible")
                    .find("textarea")
                    .click()
                    .type('Foo Bar Baz', {force: true, delay: 50})
                    .then(() => {
                        return cy.saveEvent();
                    })
                    .then(() => {
                        cy.assertEventSaved(false);
                    });
            })
            .then(() => {
                this.data.seederResult.automated_event.element_names.forEach((element_name) => {
                    cy.getByDataAttrContains('element-type-class', element_name).should('exist');
                });
            });
    });

    it('manually generated event shows correct elements', function () {
        cy.visit(this.data.eventViewUrl + '' + this.data.seederResult.manual_event.id)
            .then(() => {
                this.data.seederResult.manual_event.element_names.forEach((element_name) => {
                    cy.getByDataAttrContains('element-type-class', element_name)
                        .should('exist');
                });
            });
    });

    it('manually generated event enables edit on correct elements', function () {
        cy.visit(this.data.eventEditUrl + '' + this.data.seederResult.manual_event.id)
            .then(() => {
                cy.getByDataAttrContains('element-type-class', 'Comments')
                    .should('exist')
                    .within(()=> {
                        cy.getBySel('comments-edit').should('exist');
                        cy.getBySel('comments-view').should('not.exist');
                    });

                cy.getByDataAttrContains('element-type-class', 'HFA')
                    .should('exist')
                    .within(()=> {
                        cy.getBySel('hfa-edit').should('exist');
                        cy.getBySel('hfa-view').should('not.exist');
                    });
            });
    });

    it('manually generated event shows correct elements after save', function () {
        cy.visit(this.data.eventEditUrl + '' + this.data.seederResult.manual_event.id)
            .then(() => {
                cy.getByDataAttrContains('element-type-class', 'Comments')
                    .should("be.visible")
                    .find("textarea")
                    .click()
                    .type('Foo Bar Baz', {force: true, delay: 50})
                    .then(() => {
                        return cy.saveEvent();
                    })
                    .then(() => {
                        cy.assertEventSaved(false);
                    });
            })
            .then(() => {
                this.data.seederResult.manual_event.element_names.forEach((element_name) => {
                    cy.getByDataAttrContains('element-type-class', element_name).should('exist');
                });
            });
    });
});