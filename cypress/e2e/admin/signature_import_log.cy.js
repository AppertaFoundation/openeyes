var uniqueIdentifier;
var elementTypeId;
var YII_CSRF_TOKEN;
var base64String;

function createUniqueCode(eventId) {
    cy.createModels(
        "UniqueCodes",
        [],
        {
            'active': 1
        }
    ).then((attr) => {
        uniqueIdentifier = attr.code;
        cy.createModels(
            "UniqueCodeMapping",
            [],
            {
                'event_id': eventId,
                'unique_code_id': attr.id
            }
        );

    });
}

function blobToBase64(blob) {
    return new Promise((resolve, _) => {
        const reader = new FileReader();
        reader.onloadend = () => resolve(reader.result);
        reader.readAsDataURL(blob);
    });
}

describe('Test Signature Import Log', () => {
    beforeEach(() => {
        cy.login()
            .then(() => {
                return cy.runSeeder(null, 'CreateElementTypeSeeder');
            })
            .then((data) => {
                elementTypeId = data.element_type_id;
            })
            .then(() => {
                return cy.createEvent('OphCoCvi');
            })
            .as('event')
            .then((event) => {
                createUniqueCode(event.id);
                cy.createModels(
                    "OEModule\\OphCoCvi\\models\\Element_OphCoCvi_Esign",
                    [],
                    {
                        'event_id': event.id,
                    }
                ).then((element) => { // Save to log table

                    cy.fixture('cvi_signature_import_sample.jpg').as('imageData');
                    cy.get('@imageData').then((fileContent) => {
                        const base64Image = Cypress.Blob.base64StringToBlob(fileContent, 'image/jpeg');

                        return blobToBase64(base64Image).then((base64String)=>{
                            base64String = base64String.replace('data:image/jpeg;base64,','');

                            let signatureUrl = "/Api/sign/add";
                            let sendObj = JSON.stringify({
                                "unique_identifier": uniqueIdentifier,
                                "image": base64String,
                                "extra_info": '{"e_t_id":' + elementTypeId + ',"e_id":' + element.id + '}'
                            });
                            cy.request({
                                method: 'POST',
                                url: signatureUrl,
                                headers: {
                                    'Content-Type': 'application/json',
                                },
                                body: sendObj,
                            }).then((response) => {
                                expect(response.status).to.eq(200);

                                let url = '/DicomLogViewer/signatureList';
                                cy.visit(url);
                                cy.getBySel('result_body').find('tr')
                                    .as('resultLine')
                                    .should('have.length.greaterThan', 0);
                                cy.get('@resultLine').find('.crop_button').first().click();
                            });
                        });
                    });
                });
            });

        cy.window().then((win) => {
            YII_CSRF_TOKEN = (YII_CSRF_TOKEN === undefined) ? win['YII_CSRF_TOKEN'] : YII_CSRF_TOKEN;
        });
    });

    it('Crop signature', function () {
        document.cookie = 'YII_CSRF_TOKEN=' + YII_CSRF_TOKEN + '; path=/';
        cy.getBySel('crop_button').click().then(() => {
            cy.getBySel('answer_string').contains('successfull');
        });
    });
});