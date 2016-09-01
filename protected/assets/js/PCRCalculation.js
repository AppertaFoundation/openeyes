


/**
 * Takes the element that has been changed and worked out which eye it should be altering in PCR risk
 *
 * @param ev
 * @returns {*|jQuery|HTMLElement}
 */
function getPcrContainer(ev)
{
    var isRight = (ev.target.id.indexOf('right') > -1),
        $container = $('#ophCiExaminationPCRRiskLeftEye');

    if(isRight){
        $container = $('#ophCiExaminationPCRRiskRightEye');
    }

    return $container;
}

/**
 * Checks if no view is set in C/D in Optic Disc element
 *
 * @param ev
 * @param pcrEl
 */
function setFundalView(ev, pcrEl)
{
    if (!pcrEl) {
        pcrEl = ev.data;
    }

    var $container = getPcrContainer(ev);
    if($(ev.target).find(':selected').data('value') === 'No view'){
        $container.find(pcrEl).val('Y');
    } else {
        $container.find(pcrEl).val('N');
    }

    $(pcrEl).trigger('change');
}

/**
 * Sets Diabetes Present in PCR risk if a diabetic disorder is diagnosed.
 *
 * @param ev
 * @param pcrEl
 */
function setDiabeticDisorder(ev, pcrEl)
{
    if (!pcrEl) {
        pcrEl = ev.data;
    }

    if($('input[name^="diabetic_diagnoses"]').length){
        $(pcrEl).val('Y');
    }

    $(pcrEl).trigger('change');
}

/**
 * Sets Glaucoma Present in PCR risk if glaucoma is diagnosed.
 *
 * @param ev
 * @param pcrEl
 */
function setGlaucomaDisorder(ev, pcrEl)
{
    if (!pcrEl) {
        pcrEl = ev.data;
    }

    if($('input[name^="glaucoma_diagnoses"]').length){
        $(pcrEl).val('Y');
    }

    $(pcrEl).trigger('change');
}

/**
 * Sets the grade of the surgeon
 *
 * Does an ajax lookup to get the grade of the surgeon based on the ID and sets the grade in PCR risk
 *
 * @param ev
 * @param pcrEl
 */
function setSurgeonFromNote(ev, pcrEl)
{
    if(!pcrEl){
        pcrEl = ev.data;
    }

    var surgeonId = $(ev.target).val();
    if(!surgeonId){
        $(pcrEl).val('');
        $(pcrEl).trigger('change');
        return;
    }

    $.ajax({
        'type': 'GET',
        'url': '/user/surgeonGrade/',
        'data': {'id': surgeonId},
        'success': function(data){
            $(pcrEl).val(data.id);
            $(pcrEl).trigger('change');
        }
    });
    $(pcrEl).trigger('change');
}

/**
 * Sets whether or not the cataract is brunescent in PCR risk
 *
 * @param ev
 * @param pcrEl
 */
function setPcrBrunescent(ev, pcrEl)
{
    if(!pcrEl){
        pcrEl = ev.data.pcr;
    }

    var $container = getPcrContainer(ev);
    var $cataractDrop = $(ev.target);
    
    var isRight = (ev.target.id.indexOf('right') > -1);
    var $related = isRight ? $(ev.data.related.right) : $(ev.data.related.left);

    var value = $cataractDrop.find(':selected').data('value');
    var related_value = $related.find(':selected').data('value');
    if( (value === 'Brunescent' || value === 'White') || (related_value === "Brunescent" || related_value === "White") ){
        $container.find(pcrEl).val('Y');
    } else {
        $container.find(pcrEl).val('N');
    }
    $(pcrEl).trigger('change');

}

/**
 * Sets PXF/Phacodonesis from Anteriror Segment
 * @param ev
 * @param pcrEl
 */
function setPcrPxf(ev, pcrEl)
{    
    if(!pcrEl){
        pcrEl = ev.data.pcr;
    }

    var $container = getPcrContainer(ev);
    var $related = $(ev.data.related);
    
    if(ev.target.checked || $related.is(':checked') ){
        $container.find(pcrEl).val('Y');
    } else {
        $container.find(pcrEl).val('N');
    }
    $(pcrEl).trigger('change');

}

/**
 * Sets Pupil Size
 * @param ev
 * @param pcrEl
 */
function setPcrPupil(ev, pcrEl)
{
    if(!pcrEl){
        pcrEl = ev.data;
    }

    var $container = getPcrContainer(ev);
    $container.find(pcrEl).val($(ev.target).val());
    $(pcrEl).trigger('change');

}

/**
 * Maps elements in examination or op not to their respective elements in PCR risk so changes in the
 * examination are reflected in the PCR risk calculation automatically
 */
function mapExaminationToPcr()
{
    var examinationMap = {
          "#Element_OphTrOperationnote_Surgeon_surgeon_id": {
              "pcr": '.pcr_doctor_grade',
              "func": setSurgeonFromNote,
              "init": true
          },
          "#OEModule_OphCiExamination_models_Element_OphCiExamination_AnteriorSegment_right_nuclear_id,#OEModule_OphCiExamination_models_Element_OphCiExamination_AnteriorSegment_left_nuclear_id": {
              "pcr": {    "related" : {
                  "left" : "#OEModule_OphCiExamination_models_Element_OphCiExamination_AnteriorSegment_left_cortical_id",
                  "right" : "#OEModule_OphCiExamination_models_Element_OphCiExamination_AnteriorSegment_right_cortical_id"
              },
                  "pcr" : '.pcrrisk_brunescent_white_cataract'},
              "func": setPcrBrunescent,
              "init": true
          },
          "#OEModule_OphCiExamination_models_Element_OphCiExamination_AnteriorSegment_right_cortical_id,#OEModule_OphCiExamination_models_Element_OphCiExamination_AnteriorSegment_left_cortical_id": {
              "pcr": {    "related": {
                  "left" : "#OEModule_OphCiExamination_models_Element_OphCiExamination_AnteriorSegment_left_nuclear_id",
                  "right" : "#OEModule_OphCiExamination_models_Element_OphCiExamination_AnteriorSegment_right_nuclear_id"
              } ,
                  "pcr": '.pcrrisk_brunescent_white_cataract'},
              "func": setPcrBrunescent,
              "init": true
          },

          ":checkbox[id*='_pxe_control']": {
              "pcr":  {   "related": ":checkbox[id*='_phako']",
                  "pcr": '.pcrrisk_pxf_phako'
              },
              "func": setPcrPxf,
              "init": true
          },
          ":checkbox[id*='_phako']": {
              "pcr":  {   "related": ":checkbox[id*='_pxe_control']",
                  "pcr": '.pcrrisk_pxf_phako'
              },
              "func": setPcrPxf,
              "init": true
          },

          ":input[id*='_pupilSize_control']": {
              "pcr":  '.pcrrisk_pupil_size',
              "func": setPcrPupil,
              "init": true
          },
          ":input[name^='diabetic_diagnoses']": {
              "pcr": '.pcrrisk_diabetic',
              "func": setDiabeticDisorder,
              "init": true
          },
          ":input[name^='glaucoma_diagnoses']": {
              "pcr": '.pcrrisk_glaucoma',
              "func": setGlaucomaDisorder,
              "init": true
          },
          "#OEModule_OphCiExamination_models_Element_OphCiExamination_OpticDisc_right_cd_ratio_id,#OEModule_OphCiExamination_models_Element_OphCiExamination_OpticDisc_left_cd_ratio_id": {
              "pcr": '.pcrrisk_no_fundal_view',
              "func": setFundalView,
              "init": true
          }
      },
      examinationObj,
      examinationEl;

    for(examinationEl in examinationMap){
        if(examinationMap.hasOwnProperty(examinationEl)){
            examinationObj = examinationMap[examinationEl];
            if(typeof examinationObj.func === 'function'){
                $('#event-content').on('change', examinationEl, examinationObj.pcr, examinationObj.func);
            }
            //Some stuff is set from PHP on page load, some is not so we need to init them
            if(typeof examinationObj.init !== 'undefined' && examinationObj.init){

                if(typeof examinationObj.pcr === 'object'){
                    $(examinationEl).trigger('change', [examinationObj.pcr.pcr]);
                }else{
                    $(examinationEl).trigger('change', [examinationObj.pcr]);
                }
            }
        }
    }
}

/**
 * Capitalises the first letter of a string
 *
 * @param input
 * @returns {string}
 */
function capitalizeFirstLetter( input) {
    return input.charAt(0).toUpperCase() + input.slice(1);
}

/**
 * Collects the data from the form in to an object
 *
 * @param side
 * @returns {{}}
 */
function collectValues( side ){
    var pcrData = {},
        $eyeSide = $('#ophCiExaminationPCRRisk' + side + 'Eye');

    pcrData.age = $eyeSide.find(":input[id$='age']").val();
    pcrData.gender = $eyeSide.find(":input[id$='gender']").val();
    pcrData.glaucoma = $eyeSide.find("select[id$='glaucoma']").val();
    pcrData.diabetic = $eyeSide.find("select[id$='diabetic']").val();
    pcrData.fundalview = $eyeSide.find("select[id$='no_fundal_view']").val();
    pcrData.brunescentwhitecataract = $eyeSide.find("select[id$='brunescent_white_cataract']").val();
    pcrData.pxf = $eyeSide.find("select[id$='pxf_phako']").val();
    pcrData.pupilsize = $eyeSide.find("select[id$='pupil_size']").val();
    pcrData.axiallength = $eyeSide.find("select[id$='axial_length']").val();
    pcrData.alpareceptorblocker = $eyeSide.find("select[id$='arb']").val();
    pcrData.abletolieflat = $eyeSide.find("select[id$='abletolieflat']").val();
    pcrData.doctorgrade = $eyeSide.find("select[id$='doctor_grade_id']").val();

    return pcrData;
}

/**
 *
 * @param inputValues
 * @returns {*}
 */
function calculateORValue( inputValues )
{
    if(Object.keys(inputValues).length === 0){
        return 0;
    }

    var OR ={};
    var orMultiplied = 1;  // base value

    // multipliers for the attributes and selected values
    OR.age = {'1':1, '2':1.14, '3':1.42, '4':1.58, '5':2.37};
    OR.gender = {'Male':1.28, 'Female':1, 'Other':1.14, 'Unknown':1.14};
    OR.glaucoma = {'Y':1.30, 'N':1, 'NK':1};
    OR.diabetic = {'Y':1.63, 'N':1, 'NK':1};
    OR.fundalview = {'Y':2.46, 'N':1, 'NK':1};
    OR.brunescentwhitecataract = {'Y':2.99, 'N':1, 'NK':1};
    OR.pxf = {'Y':2.92, 'N':1, 'NK':1};
    OR.pupilsize = {'Small': 1.45, 'Medium':1.14, 'Large':1, 'NK':1};
    OR.axiallength = {'1':1, '2':1.47};
    OR.alpareceptorblocker = {'Y':1.51, 'N':1, 'NK':1};
    OR.abletolieflat = {'Y':1, 'N':1.27};
    OR.doctorgrade = {};

    if(Object.keys(inputValues).length !== Object.keys(OR).length){
        return 0;
    }

    for (var key in inputValues) {
        if(!inputValues.hasOwnProperty(key)){
            continue;
        }
        var riskFactor;
        //if we have a key to factor relationship use that, otherwise the factor is in the input value itself.
        if(OR[key].hasOwnProperty(inputValues[key])){
            riskFactor = OR[key][inputValues[key]];
        } else {
            riskFactor = inputValues[key];
        }
        orMultiplied *= riskFactor;
    }

    return orMultiplied;
}

/**
 * Calculates the PCR risk for a given side
 *
 * @param side
 */
function pcrCalculate( side ){

    side = capitalizeFirstLetter(side);  // we use this to keep camelCase div names

    var pcrDataValues = collectValues( side),
        ORValue = calculateORValue( pcrDataValues),
        pcrRisk,
        excessRisk,
        pcrColor,
        averageRiskConst;

    if( ORValue ) {
        pcrRisk = ORValue * (0.00736 / (1 - 0.00736)) / (1 + (ORValue * 0.00736 / (1 - 0.00736))) * 100;
        averageRiskConst = 1.92;
        excessRisk = pcrRisk / averageRiskConst;
        excessRisk = excessRisk.toFixed(2);
        pcrRisk = pcrRisk.toFixed(2);

        if (pcrRisk <= 1) {
            pcrColor = 'green';
        } else if (pcrRisk > 1 && pcrRisk <= 5) {
            pcrColor = 'orange';
        } else {
            pcrColor = 'red';
        }
    }else{
        pcrRisk = "N/A";
        excessRisk = "N/A";
        pcrColor = 'blue';
    }
    $('#ophCiExaminationPCRRisk'+side+'Eye').find('#pcr-risk-div').css('background', pcrColor);
    $('#ophCiExaminationPCRRisk'+side+'Eye').find('.pcr-span').html(pcrRisk);
    $('#ophCiExaminationPCRRisk'+side+'Eye').find('.pcr-erisk').html(excessRisk);

    $('#ophCiExaminationPCRRisk'+side+'EyeLabel').find('a').css('color', pcrColor);
    $('#ophCiExaminationPCRRisk'+side+'EyeLabel').find('.pcr-span1').html(pcrRisk);

    $('#ophCiExaminationPCRRiskEyeLabel').find('a').css('color', pcrColor);
    $('#ophCiExaminationPCRRiskEyeLabel').find('.pcr-span1').html(pcrRisk);
    //$('#ophCiExaminationPCRRisk'+side+'EyeLabel').find('.pcr-span1').css('color', pcrColor);
    if(pcrRisk !== 'N/A'){
        $('#Element_OphTrOperationnote_Cataract_pcr_risk').val(pcrRisk);
    }
}

$(document).ready(function()
{
    //Map the elements
    mapExaminationToPcr();
    //Make the initial calculations
    pcrCalculate('left');
    pcrCalculate('right');

    $(document.body).on('change','#ophCiExaminationPCRRiskLeftEye',function(){
        pcrCalculate('left');
    });

    $(document.body).on('change','#ophCiExaminationPCRRiskRightEye',function(){
        pcrCalculate('right');
    });

});