describe('post op complications admin screen behaviour', () => {
    const complicationNames = [
        'complication one ' + Date.now(),
        'complication two ' + Date.now()
    ]
    const subspecialtyName = 'test subspecialty ' + Date.now();

    before(() => {
        complicationNames.forEach((complicationName) => {
            cy.createModels(
                "OEModule\\OphCiExamination\\models\\OphCiExamination_PostOpComplications",
                [],
                {
                    'name': complicationName
                }
            );
        });
        cy.createModels(
            "Subspecialty",
            [],
            {
                'name': subspecialtyName
            }
        );
    });

    beforeEach(() => {
        cy.login()
            .then((context) => {
                context.visit_url = '/OphCiExamination/admin/postOpComplications?subspecialty_id=' + context.body.subspecialty_id;
            })
            .then((context) => {
                 return cy.runSeeder(
                    'OphCiExamination',
                    'PostOpComplicationsAdminSeeder',
                    subspecialtyName                    
                ).then((seederResult) => {
                    return {
                        visit_url: context.visit_url,
                        post_op_complications: seederResult.post_op_complications
                    }
                });
            })
            .then((context) => {
                return cy.visit(context.visit_url).then(() => {
                    return context.post_op_complications;
                });
            })
            .as('postOpComplications');
    });

    it('loads with the correct number of complications for institution and subspeciality', function () {
        const complicationsList = cy.getBySel('selected-complications')
            .find('li');

        complicationsList.should('have.length', this.postOpComplications['defaultSubspecialty'].length);
    });

    it('select and removing complications works as expected', function () {
        const expectedComplicationsCount = this.postOpComplications['defaultSubspecialty'].length + complicationNames.length;

        const complicationsSelect = cy.getBySel('complications-select');
        
        complicationNames.forEach((complicationName) => {
            complicationsSelect.should('be.visible').select(complicationName, {force: true});
        });

        const complicationsList = cy.getBySel('selected-complications')
                            .find('li');
        
        complicationsList.should('have.length', expectedComplicationsCount);
        
        // remove the first item and make sure it has been removed from the list
        complicationsList.first()
            .find('i')
            .click({force: true})
            .then(() => {
                cy.getBySel('selected-complications')
                            .find('li')
                            .should('have.length', expectedComplicationsCount - 1);
            });
    });

    it('switching to new subspeciality loads no selected complications', function() {
        const subspecialitySelect = cy.get('[data-test="subspeciality-wrapper"] select');
        subspecialitySelect.should('be.visible').select(subspecialtyName, {force: true});

        const complicationsList = cy.getBySel('selected-complications')
            .find('li');

        complicationsList.should('not.exist');
        
    });
});

function getRandomInt(min, max){      
    return Math.floor(Math.random() * (max - min + 1)) + min;    
}       