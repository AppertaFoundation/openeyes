describe('macula element behaviour', () => {
    before(() => {
        cy.login()
            .then(() => {
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
                cy.addExaminationElement('Macula');
            });
    });

    describe('diagnoses are automatically added or removed by eyedraw doodles', () => {
        before(() => {
            cy.addExaminationElement('Ophthalmic Diagnoses');
        });

        // Note that this doesn't cover doodles having different disorders
        // based upon doodle parameters.
        const doodlesWithDisorders = [
            ['Geographic atrophy', 'Nonexudative age-related macular degeneration'],
            ['Epiretinal membrane', 'Epiretinal membrane'],
            ['Macroaneurysm', 'Retinal macroaneurysm'],
            ['Retinal vein occluson', 'Venous retinal branch occlusion'],
            ['Retinal artery occlusion', 'Retinal artery occlusion'],
            ['Extra-foveal CNV', 'Choroidal retinal neovascularisation'],
            ['Cystoid macular oedema', 'Cystoid macular oedema'],
            ['Retinal neovascularisation', 'Retinal neovascularisation'],
            ['Pigment epithelium detachment', 'Serous detachment of retinal pigment epithelium'],
            ['Vitreous haemorrhage', 'Vitreous haemorrhage', true],
            ['Parafoveal telangiectasia', 'Parafoveal telangiectasia'],
            ['Choroidal haemorrhage', 'Choroidal haemorrhage'],
            ['Choroidal mass/pigment', 'Naevus of choroid'],
            ['Polypoidal choroidal vasculopathy', 'Idiopathic polypoidal choroidal vasculopathy'],
            ['Macular dystrophy', 'Hereditary macular dystrophy'],
            ['Central Serous Retinopathy', 'Central serous chorioretinopathy']
        ];
        
        
        it(`adds the correct diagnosis for doodles added to the eyedraw, and removes them again.`, () => {
            doodlesWithDisorders.forEach(([doodleName, disorderName, doodleIsInDrawer]) => {
                cy.addEyedrawDoodleInElement('Macula', doodleName, 'right', doodleIsInDrawer);
                cy.get('#OEModule_OphCiExamination_models_Element_OphCiExamination_Diagnoses_diagnoses_table').should('include.text', disorderName);
                cy.removeEyedrawDoodleInElement('Macula', doodleName, 'right');
                cy.get('#OEModule_OphCiExamination_models_Element_OphCiExamination_Diagnoses_diagnoses_table').should('not.include.text', disorderName);
            });
        });

    });
    
});