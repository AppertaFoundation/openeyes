describe('admin for selecting elements for generic subtypes works correctly', () => {

    const dateString = Date.now();
    const eventSubtypeName = 'test event subtype ' + dateString;
    const widgetsToSelect = (() => {
        const widgetIds = [];
        // @see \OEModule\OphGeneric\modules\OphGenericAdmin\controllers\DefaultController::MANUAL_ELEMENT_CLASSES
        const widgetOptions = ['Comments', 'Device Information', 'HFA'];
        widgetOptions.forEach((widget) => {
            if (Math.random() < 0.5) {
                widgetIds.push(widget);
            }
        });

        if(widgetIds.length < 1) {
            widgetIds.push(widgetOptions[0]);
        }

        return widgetIds;
    })();

    before(() => {
        cy.createModels(
            "EventSubtype",
            [],
            {
                'event_subtype': eventSubtypeName,
                'display_name': eventSubtypeName
            }
        );
    });

    beforeEach(() => {
        cy.login()
            .then((context) => {
                cy.visit('/OphGeneric/admin/Default/listEventSubTypes')
                    .then(() => {
                        cy.getByDataAttrContains('id', dateString)
                        .scrollIntoView()
                        .should('be.visible')
                            .click({force: true});
                    });
            })
    });

    it('displays the correct event subtype name', function() {
        cy.contains(eventSubtypeName);
    });

    it('can enable manual status and edit icon and elements', function() {
        // check that no icon is selected on load
        cy.getByElementName('EventSubtype[icon_id]')
            .each((element) => {
                cy.wrap(element).scrollIntoView()
                    .should('not.be.checked');
            });

        // check no widgets selected
        cy.getBySel('assessment-widget-row')
            .should('not.exist');

        // select an icon
        cy.getByElementName('EventSubtype[icon_id]')
            .last()
            .scrollIntoView()
            .check({force: true});

        // select one or more widgets
        widgetsToSelect.forEach((widget) => {
            cy.getBySel('choose-widgets-select')
                .scrollIntoView()
                .select(widget);
        });

        // enable manual creation
        cy.getBySel('manual-entry-checkbox')
            .scrollIntoView()
            .check({force: true});

        cy.saveEvent()
            .then(() => {
                cy.getByDataAttrContains('id', dateString)
                .scrollIntoView()
                .should('be.visible')
                    .click({force: true});
            })
            .then(() => {
                cy.getByElementName('EventSubtype[icon_id]')
                    .last()
                    .should('be.checked');

                cy.getBySel('assessment-widget-row')
                    .should('have.length', widgetsToSelect.length);

                widgetsToSelect.forEach((widget) => {
                    cy.getBySel('assessment-widgets').contains(widget);
                });
            });

        // change icon, remove an element and make changes apply
        cy.getByElementName('EventSubtype[icon_id]')
            .first()
            .scrollIntoView()
            .check({force: true});

        cy.getBySel('assessment-widget-row')
            .last()
            .within(() => {
                cy.get('button')
                    .scrollIntoView()
                    .click({force: true});
            });

        cy.saveEvent()
            .then(() => {
                cy.getByDataAttrContains('id', dateString)
                .scrollIntoView()
                .should('be.visible')
                    .click({force: true});
            })
            .then(() => {
                cy.getByElementName('EventSubtype[icon_id]')
                    .first()
                    .should('be.checked');

                if((widgetsToSelect.length - 1) < 1) {
                    cy.getBySel('assessment-widget-row')
                    .should('not.exist');
                } else {
                    cy.getBySel('assessment-widget-row')
                        .should('have.length', widgetsToSelect.length - 1);
                }
            });
    });
});
