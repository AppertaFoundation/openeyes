describe('Managing a patient pathway in the worklist screen', () => {
    let worklistPatient;
    before(() => {
        cy.createModels('Worklist', [['withStepsOfType', ['checkin', 'discharge']]])
            .then((worklist) => {
                return cy.createModels('WorklistPatient', [], {'worklist_id': worklist.id});
            }).as('worklistPatient');
    });

    beforeEach(() => {
        cy.login().as('loggedInUser')
            .then(() => {
                cy.clock(new Date().getTime(), ["Date"]);
                cy.visitWorklist();

                // Close the navbar if it is open
                cy.getBySel('nav-worklist-btn').then(($ele) => {
                    if ($ele.hasClass('open')) {
                        $ele.click();
                    }
                });
            });
    });

    it('add a check-in step', function () {
        // Assigning the worklistPatient object to the worklistPatient variable because the alias worklistPatient is not accessible in the next tests
        worklistPatient = this.worklistPatient;

        //Add a general task step  to avoid completing the pathway
        cy.getWorklist(this.worklistPatient.worklist_id)
        .within(() => {
            cy.get('table tbody tr').should('have.length', 1);
        });

        // Intercepting the Network request to wait for the response.
        cy.intercept({
            method: 'GET',
            url: '/worklist/getPathStep*',
        }).as('getPathStep');

        cy.getBySel(`arr-step-${this.worklistPatient.worklist_id}`).click({force: true})

        cy.wait('@getPathStep');

        // Checking if the arrived count has been incremented by one after check-in the patient.
        cy.getWorklistArrivedFilterCount().then(($el) => {
            const arrivedCount = +$el.text();
            cy.getBySel('step-done').click();

            cy.getWorklistArrivedFilterCount().should(($el2) => {
                expect(+$el2.text()).to.eq(arrivedCount + 1);
            });
        });

        // Click on the arrived filter.
        cy.getWorklistArrivedFilter().click();

        cy.getWorklist(this.worklistPatient.worklist_id)
            .within(() => {
                // Assert that the patient shows in the Arrived filter
                cy.get('table tbody tr').should('have.length', 1);

                // Assert that the total timer and wait timer is visible.
                cy.getBySel('wait-pathstep').should('be.visible');
                cy.getBySel('wait-duration').should('be.visible');
            });
    });

    //Add check out step
    it('add a check-out step', function () {
        // Assert that the total duration timer is not stopped before check-out
        cy.getWorklist(worklistPatient.worklist_id).within(() => {
            cy.getBySel('wait-duration').should('not.have.class', 'stopped')
        });

        // Click on Checkout
        cy.getBySel(`discharge-step-${worklistPatient.worklist_id}`).click({force: true});
        cy.getBySel('step-checkout').click({force: true});

        // Assert that the total duration timer is stopped after check-out
        cy.getWorklist(worklistPatient.worklist_id).within(() => {
            cy.getBySel('wait-duration').should('have.class', 'stopped');
            cy.getBySel('wait-pathstep').should('not.exist');

            // Add new General Task step after check out
            cy.getBySel('add-step').click();
        });

        cy.getBySel('path-step-general-task').click();
        cy.getBySel('path-step-task-name').type('New Task');
        cy.getBySel('path-step-add-pathway').click();

        // Start the General Task step
        cy.getBySel('new-task').click();
        cy.getBySel('next-generic-step').click();

        // Travel ~1 minute to the future without waiting
        cy.tick(62000);

        //Without reloading it shows that pathway is complete; with reloading it shows pathway not completed
        cy.reload();

        cy.getWorklist(worklistPatient.worklist_id).within(() => {
            cy.getBySel('wait-duration-mins').should('have.text','0:00');
            cy.getBySel('pathway-status').find('i').should('have.class','js-pathway-finish');

            //assert that pathway status is not completed
            cy.getBySel('pathway-status').find('i').should('have.attr', 'data-tooltip-content').and('equal', 'Quick complete pathway')
        })
    });

    it('remove last todo pathway step', function () {

        cy.getWorklist(worklistPatient.worklist_id).within(() => {
            // Add new General Task step after check out
            cy.getBySel('add-step').click();
        });

        cy.getBySel("path-step-decision-\\/-review").click();

        cy.getBySel(`fork-step-${worklistPatient.worklist_id}`).should('exist');

        cy.getBySel('undo-add-step').click();

        cy.getBySel(`fork-step-${worklistPatient.worklist_id}`).should('not.exist');

        cy.reload();

        cy.getBySel(`fork-step-${worklistPatient.worklist_id}`).should('not.exist');
    });

    it('can select and deselect all pathway step adders in a worklist', function () {
        cy.getWorklist(worklistPatient.worklist_id).within(() => {
            cy.getBySel('add-step-checkbox').should('not.be.checked');

            cy.getBySel('add-step').first().click();

            cy.getBySel('add-step-checkbox').should('be.checked');

            cy.getBySel('add-step').first().click();

            cy.getBySel('add-step-checkbox').should('not.be.checked');
        });
    });

    it('can add Drug Administration Preset Order', function () {
        cy.intercept('*worklist/addStepToPathway*').as('addStepToPathway');
        cy.intercept('*worklist/getPathStep*').as('getPathStep');
        cy.intercept('*OphDrPGDPSD/PSD/unlockPSD*').as('unlockPSD');

        cy.getBySel('close-worklist-adder-btn');

        // make it 'static' so we preserve TR we work with, otherwise cy.get('@tr') would re-run our cy.get()
        cy.get('table.oec-patients tbody tr:not(:has(.i-drug-admin))').first().as('tr', { type: 'static' });

        cy.get('@tr').then(tr => {
                cy.addPathStep(`#${tr.attr('id')}`, 'path-step-drug-administration-preset-order');
            });

        cy.get('div.popup-path-step-options').should('be.visible').within(popup => {
            cy.contains('Todo: Next').click();
            cy.get('button.js-add-pathway').click();
        });
        cy.wait('@addStepToPathway');

        cy.get('@tr').invoke('attr', 'id').then(trId => {

            // fire a new get() as the DOM of our TR has changed
            cy.get(`#${trId}`).find('td.js-pathway-container div.pathway').within(pathway => {
                cy.getBySel('drug-administration-step')
                    .should('exist')
                    .click({force: true});
            });
        });

        cy.wait('@getPathStep');

        cy.get(`#worklist-administration-form`).should('be.visible')
            .within(form => {
                cy.get(`input.user-pin-entry`).type(this.loggedInUser.body.pincode);
                cy.get(`button.try-pin.js-unlock`).should('be.visible').click();
            });
        cy.wait('@unlockPSD');

        // query again as the content of the DOM has changed
        cy.get(`#worklist-administration-form`).within(form => {

            cy.get('[type="checkbox"]').check();
            cy.contains(`Confirm Administration`).click();
        });

        cy.get(`div.oe-pathstep-popup`).should('be.visible').within(popup => {
            cy.get(`div.step-status.done`).should('be.visible').and('have.text', 'Completed');
        });

        cy.get('@tr').invoke('attr', 'id').then(trId => {
            // fire a new get() as the DOM of our TR has changed
            cy.get(`#${trId}`).find('td.js-pathway-container div.pathway').within(pathway => {
                cy.get(`span`).first()
                    .should('be.visible')
                    .and('have.class', 'oe-pathstep-btn')
                    .and('have.class', 'done')
                    .and('have.class', 'process');
            });
        });
    });
});
