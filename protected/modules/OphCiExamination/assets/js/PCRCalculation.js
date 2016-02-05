var timesSelectClicked = 0;

/**
 * We need this function to trigger select click event in Chrome (select>option click event worked in Firefox, but had issues in Chrome)
 * @param e
 */
function selectClicked(e){
    if (timesSelectClicked == 0)
    {
        timesSelectClicked += 1;
    }
    else if (timesSelectClicked == 1)
    {
        timesSelectClicked = 0;
        removeNotKnownAlert($(e.target));
    }
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
                "pcr": 'select[name="brunescent_white_cataract"]',
                "func": setPcrBrunescent
            },
            ":input[id*='_pxe_control']": {
                "pcr":  'select[name="pxf_phako"]',
                "func": setPcrPxf
            },
            ":input[id*='_pupilSize_control']": {
                "pcr":  'select[name="pupil_size"]',
                "func": setPcrPupil
            },
            ":input[name^='diabetic_diagnoses']": {
                "pcr": 'select[name="diabetic"]',
                "func": setDiabeticDisorder,
                "init": true
            },
            ":input[name^='glaucoma_diagnoses']": {
                "pcr": 'select[name="glaucoma"]',
                "func": setGlaucomaDisorder,
                "init": true
            },
            "#OEModule_OphCiExamination_models_Element_OphCiExamination_OpticDisc_right_cd_ratio_id,#OEModule_OphCiExamination_models_Element_OphCiExamination_OpticDisc_left_cd_ratio_id": {
                "pcr": 'select[name="no_fundal_view"]',
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
                $(examinationEl).trigger('change', [examinationObj.pcr]);
            }
        }
    }
}

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
        pcrEl = ev.data;
    }

    var $container = getPcrContainer(ev);
    var $cataractDrop = $(ev.target);
    if($cataractDrop.find(':selected').data('value') === 'Brunescent'){
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
        pcrEl = ev.data;
    }

    var $container = getPcrContainer(ev);

    if(ev.target.checked){
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
 * Hides the Not Known alert message for the selected element
 * @param ev
 *
 */
function removeNotKnownAlert(element){

    var containerdiv = $(element).closest('[id*="ophCiExaminationPCRRisk"]');

    containerdiv.find('#'+$(element).attr('id')+'_nk').hide();
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
    var pcrdata = {},
        $eyeSide = $('#ophCiExaminationPCRRisk' + side + 'Eye');

    pcrdata.age = $eyeSide.find(":input[name='age']").val();
    pcrdata.gender = $eyeSide.find(":input[name='gender']").val();
    pcrdata.glaucoma = $eyeSide.find("select[name='glaucoma']").val();
    pcrdata.diabetic = $eyeSide.find("select[name='diabetic']").val();
    pcrdata.fundalview = $eyeSide.find("select[name='no_fundal_view']").val();
    pcrdata.brunescentwhitecataract = $eyeSide.find("select[name='brunescent_white_cataract']").val();
    pcrdata.pxf = $eyeSide.find("select[name='pxf_phako']").val();
    pcrdata.pupilsize = $eyeSide.find("select[name='pupil_size']").val();
    pcrdata.axiallength = $eyeSide.find("select[name='axial_length']").val();
    pcrdata.alpareceptorblocker = $eyeSide.find("select[name='arb']").val();
    pcrdata.abletolieflat = $eyeSide.find("select[name='abletolieflat']").val();
    pcrdata.doctorgrade = $eyeSide.find("select[name='doctor_grade_id']").val();

    return pcrdata;
}

/**
 *
 * @param inputValues
 * @returns {*}
 */
function calculateORValue( inputValues ){
    var OR ={};
    var ORMultiplicated = 1;  // base value

    // multipliers for the attributes and selected values
    OR.age = {'1':1, '2':1.14, '3':1.42, '4':1.58, '5':2.37};
    OR.gender = {'Male':1.28, 'Female':1, 'Other':1.14, 'Unknown':1.14};
    OR.glaucoma = {'Y':1.30, 'N':1};
    OR.diabetic = {'Y':1.63, 'N':1};
    OR.fundalview = {'Y':2.46, 'N':1};
    OR.brunescentwhitecataract = {'Y':2.99, 'N':1};
    OR.pxf = {'Y':2.92, 'N':1};
    OR.pupilsize = {'Small': 1.45, 'Medium':1.14, 'Large':1};
    OR.axiallength = {'1':1, '2':1.47};
    OR.alpareceptorblocker = {'Y':1.51, 'N':1};
    OR.abletolieflat = {'Y':1, 'N':1.27};
    /*
     1 - Consultant
     2 - Associate specialist
     3 - Trust doctor  // !!!??? Staff grade??
     4 - Fellow
     5 - Specialist Registrar
     6 - Senior House Officer
     7 - House officer  -- ???? no value specified!! using: 1
     */
    OR.doctorgrade = {'1':1, '2':0.87, '3':0.36, '4':1.65, '5':1.60, '6': 3.73, '7':1};

    for (var key in inputValues) {
        if( inputValues[key] == "NK" || inputValues[key] == 0){
            return false;
        }
        ORMultiplicated *= OR[key][inputValues[key]];
    }
    return ORMultiplicated;
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

    // this is a hack for Chrome and IE
    $(document.body).on('click keypress', '#glaucoma', function(e){selectClicked(e);});

    // this one works in Firefox
    $('#glaucoma>option').click(function(event){
       removeNotKnownAlert($(this).parent());
    });

    $(document.body).on('click keypress', '#pxf_phako', function(e){selectClicked(e);});
    $('#pxf_phako>option').click(function(event){
        removeNotKnownAlert($(this).parent());
    });

    $(document.body).on('click keypress', '#diabetic', function(e){selectClicked(e);});
    $('#diabetic>option').click(function(event){
        removeNotKnownAlert($(this).parent());
    });

    $(document.body).on('click keypress', '#axial_length', function(e){selectClicked(e);});
    $('#axial_length>option').click(function(event){
        removeNotKnownAlert($(this).parent());
    });

    $(document.body).on('click keypress', '#no_fundal_view', function(e){selectClicked(e);});
    $('#no_fundal_view>option').click(function(event){
        removeNotKnownAlert($(this).parent());
    });

    $(document.body).on('click keypress', '#arb', function(e){selectClicked(e);});
    $('#arb>option').click(function(event){
        removeNotKnownAlert($(this).parent());
    });

});