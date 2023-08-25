describe('Managing a patient pathway in the worklist screen', () => {
    let worklistPatient;
    before(() => {
        cy.createModels('Worklist', [['withStepsOfType', ['checkin', 'discharge']]])
            .then((worklist) => {
                return cy.createModels('WorklistPatient', [], {'worklist_id': worklist.id});
            }).as('worklistPatient');
    });

    beforeEach(() => {
        cy.login()
            .then(() => {
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

        //Waiting for one minute to check the timer is not increasing after the check-out
        cy.wait(62000);

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
})
