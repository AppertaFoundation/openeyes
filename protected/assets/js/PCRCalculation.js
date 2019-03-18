/**
 * Takes the element that has been changed and worked out which eye it should be altering in PCR risk
 *
 * @param ev
 * @returns {*|jQuery|HTMLElement}
 */
function getPcrContainer(ev) {
    var side = $(ev.target).closest('.js-element-eye').attr('data-side');

    //for future debugging
    if(!side){
        console.log(ev);
        console.error('.js-element-eye or it\'s data attribute not found');
    }

    return $('.js-pcr-' + side.toLowerCase()).parent();
}

/**
 * Checks if no view is set in C/D in Optic Disc element
 *
 * @param ev
 * @param pcrEl
 */
function setFundalView(ev, pcrEl) {
    if (!pcrEl) {
        pcrEl = ev.data;
    }

    var $container = getPcrContainer(ev);
    if ($(ev.target).find(':selected').data('value') === 'Not checked' ) {

        return null;
    }
    if ($(ev.target).find(':selected').data('value') === 'No view') {
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
function setDiabeticDisorder(ev, pcrEl) {
    if (!pcrEl) {
        pcrEl = ev.data;
    }

    if ($('input[name^="diabetic_diagnoses"]').filter('[value=true],[value="1"]').length) {
        $(pcrEl).val('Y');
    } else {
        $(pcrEl).val('N');
    }

    $(pcrEl).trigger('change');
}

/**
 * Sets Glaucoma Present in PCR risk if glaucoma is diagnosed.
 *
 * @param ev
 * @param pcrEl
 */
function setGlaucomaDisorder(ev, pcrEl) {
    if (!pcrEl) {
        pcrEl = ev.data;
    }

    if ($('input[name^="glaucoma_diagnoses"]').filter('[value=true],[value="1"]').length) {
        $(pcrEl).val('Y');
    } else {
        $(pcrEl).val('N');
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
function setSurgeonFromNote(ev, pcrEl) {
    if (!pcrEl) {
        pcrEl = ev.data;
    }

    var surgeonId = $(ev.target).val();
    if (!surgeonId) {
        $(pcrEl).val('');
        $(pcrEl).trigger('change');
        return;
    }

    $.ajax({
        'type': 'GET',
        'url': '/user/surgeonGrade/',
        'data': {'id': surgeonId},
        'success': function (data) {
            $(pcrEl).val(data.id);
            $(pcrEl).trigger('change');
        }
    });
    $(pcrEl).trigger('change');
}

/**
 * Gets the Eyedraw string for the classification set on the provided input element
 * Set up for the cataract fields, but could be utilised for others as well.
 *
 * @param $el
 * @returns {string}
 */
function getEyedrawValue($el) {
    var reverseMap = $el.data('reverse-eyedraw-map');
    if (!reverseMap) {
        // cache the reverseMap for multiple state changes to the given element
        var map = $el.data('eyedraw-map');
        reverseMap = {};
        for (var i in map) {
            if (map.hasOwnProperty(i))
                reverseMap[map[i]] = i;
        }
        $el.data('reverse-eyedraw-map', reverseMap);
    }

    var val = $el.val();
    if (val in reverseMap)
        return reverseMap[val];
    return '';
}

/**
 * Sets whether or not the cataract is brunescent in PCR risk
 *
 * @param ev
 * @param pcrEl
 */
function setPcrBrunescent(ev, pcrEl) {
    if (!pcrEl) {
        pcrEl = ev.data.pcr;
    }

    var $container = getPcrContainer(ev);
    var $cataractDrop = $(ev.target);

    var isRight = (ev.target.id.indexOf('right') > -1);
    var $related = isRight ? $(ev.data.related.right) : $(ev.data.related.left);

    var value = getEyedrawValue($cataractDrop);
    var related_value = getEyedrawValue($related);
    if ((value === 'Brunescent' || value === 'White') || (related_value === "Brunescent" || related_value === "White")) {
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
function setPcrPxf(ev, pcrEl) {
    if (!pcrEl) {
        pcrEl = ev.data.pcr;
    }

    var $container = getPcrContainer(ev);
    var $related = $(ev.data.related);

    if (ev.target.checked || $related.is(':checked')) {
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
function setPcrPupil(ev, pcrEl) {
    if (!pcrEl) {
        pcrEl = ev.data;
    }

    var $container = getPcrContainer(ev);
    $container.find(pcrEl).val($(ev.target).val());

    $(pcrEl).trigger('change');

}

/**
 * Sets values related to risks
 *
 * @param ev
 * @param pcrEl
 */
function setRisks(ev) {
    var controller = $('#OEModule_OphCiExamination_models_HistoryRisks_element').data('controller');
    var alphaState = controller.getRiskStatus('alpha');
    var lieFlatState = controller.getRiskStatus('lie flat');

    var alphaPcr = $('section.OEModule_OphCiExamination_models_Element_OphCiExamination_PcrRisk .pcrrisk_arb');
    var originalAlpha = alphaPcr.val();
    if (alphaState === '0') {
        alphaPcr.val('N');
    } else if (alphaState === '1') {
        alphaPcr.val('Y');
    }
    if (alphaPcr.val() !== originalAlpha) {
        alphaPcr.trigger('change');
    }

    var lieFlatPcr = $('section.OEModule_OphCiExamination_models_Element_OphCiExamination_PcrRisk .pcr_lie_flat');
    var originalLieFlat = lieFlatPcr.val();
    if (lieFlatState === '0') {
        lieFlatPcr.val('Y');
    } else if (lieFlatState === '1') {
        lieFlatPcr.val('N');
    }
    if (lieFlatPcr.val() !== originalLieFlat) {
        lieFlatPcr.trigger('change');
    }
}

/**
 * Maps elements in examination or op not to their respective elements in PCR risk so changes in the
 * examination are reflected in the PCR risk calculation automatically
 */
function mapExaminationToPcr() {
    var left_eyedraw, right_eyedraw, risk_element;
    var examinationMap = {
            "#Element_OphTrOperationnote_Surgeon_surgeon_id": {
                "pcr": '.pcr_doctor_grade',
                "func": setSurgeonFromNote,
                "init": true
            },
            "#OEModule_OphCiExamination_models_Element_OphCiExamination_AnteriorSegment_right_nuclear_id,#OEModule_OphCiExamination_models_Element_OphCiExamination_AnteriorSegment_left_nuclear_id": {
                "pcr": {
                    "related": {
                        "left": "#OEModule_OphCiExamination_models_Element_OphCiExamination_AnteriorSegment_left_cortical_id",
                        "right": "#OEModule_OphCiExamination_models_Element_OphCiExamination_AnteriorSegment_right_cortical_id"
                    },
                    "pcr": '.pcrrisk_brunescent_white_cataract'
                },
                "func": setPcrBrunescent,
                "init": true
            },
            "#OEModule_OphCiExamination_models_Element_OphCiExamination_AnteriorSegment_right_cortical_id,#OEModule_OphCiExamination_models_Element_OphCiExamination_AnteriorSegment_left_cortical_id": {
                "pcr": {
                    "related": {
                        "left": "#OEModule_OphCiExamination_models_Element_OphCiExamination_AnteriorSegment_left_nuclear_id",
                        "right": "#OEModule_OphCiExamination_models_Element_OphCiExamination_AnteriorSegment_right_nuclear_id"
                    },
                    "pcr": '.pcrrisk_brunescent_white_cataract'
                },
                "func": setPcrBrunescent,
                "init": true
            },

            ":checkbox[id*='_pxe_control']": {
                "pcr": {
                    "related": ":checkbox[id*='_phako']",
                    "pcr": '.pcrrisk_pxf_phako'
                },
                "func": setPcrPxf,
                "init": true
            },
            ":checkbox[id*='_phako']": {
                "pcr": {
                    "related": ":checkbox[id*='_pxe_control']",
                    "pcr": '.pcrrisk_pxf_phako'
                },
                "func": setPcrPxf,
                "init": true
            },
            ":input[id*='_pupilSize_control']": {
                "pcr": '.pcrrisk_pupil_size',
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
            },
            "#OEModule_OphCiExamination_models_HistoryRisks_element input[type='radio']": {
                "pcr": undefined,
                "func": setRisks,
                "init": true
            }
        },
        examinationObj,
        examinationEl;

    for (examinationEl in examinationMap) {
        if (examinationMap.hasOwnProperty(examinationEl)) {
            examinationObj = examinationMap[examinationEl];
            if (typeof examinationObj.func === 'function') {
                $('#event-content').on('change', examinationEl, examinationObj.pcr, examinationObj.func);
            }
            //Some stuff is set from PHP on page load, some is not so we need to init them
            if (typeof examinationObj.init !== 'undefined' && examinationObj.init) {

                if (typeof examinationObj.pcr === 'object') {
                    $(examinationEl).trigger('change', [examinationObj.pcr.pcr]);
                } else {
                    $(examinationEl).trigger('change', [examinationObj.pcr]);
                }
            }
        }
    }
}

/**
 * when eyedraw element is loaded and intact hidden filed for e.g. pupilSize (input[id*='_pupilSize_control']) are not created
 * @param risk_element
 */
function loadFromHiddenFieds(risk_element){
    left_eyedraw = $("input[id$='_left_eyedraw").val();
    right_eyedraw = $("input[id$='_right_eyedraw").val();

    if (left_eyedraw) {
        left_eyedraw = JSON.parse($("input[id$='_left_eyedraw").val());
        $.each(left_eyedraw, function (key, outer_value) {
            $.each(outer_value, function (key, value) {
                if (key === 'pupilSize') {
                    risk_element.find('.left-eye').find("select.pcrrisk_pupil_size").val(value);
                }
            });
        });
    }

    if(right_eyedraw){
        right_eyedraw = JSON.parse( $("input[id$='_right_eyedraw").val() );
        $.each(right_eyedraw, function(key, outer_value) {
            $.each(outer_value, function(key, value) {
                if(key === 'pupilSize'){
                    risk_element.find('.right-eye').find("select.pcrrisk_pupil_size").val(value);
                }
            });
        });
    }
}

/**
 * Capitalises the first letter of a string
 *
 * @param input
 * @returns {string}
 */
function capitalizeFirstLetter(input) {
    return input.charAt(0).toUpperCase() + input.slice(1);
}

/**
 * Collects the data from the form in to an object
 *
 * @param $eyeSide
 * @returns {{}}
 */
function collectValues($eyeSide) {
    var pcrData = {};

    pcrData.age = $eyeSide.find(":input[id$='age']").val();
    pcrData.gender = $eyeSide.find(":input[id$='gender']").val();
    pcrData.glaucoma = $eyeSide.find("select[id$='glaucoma']").val();
    pcrData.diabetic = $eyeSide.find("select[id$='diabetic']").val();
    pcrData.fundalview = $eyeSide.find("select[id$='no_fundal_view']").val();
    pcrData.brunescentwhitecataract = $eyeSide.find("select[id$='brunescent_white_cataract']").val();
    pcrData.pxf = $eyeSide.find("select[id$='pxf_phako'], select[id$='pxf']").val();
    pcrData.pupilsize = $eyeSide.find("select[id$='pupil_size']").val();
    pcrData.axiallength = $eyeSide.find("select[id$='axial_length'], select[id$='axial_length_group']").val();
    pcrData.alphareceptorblocker = $eyeSide.find("select[id$='arb'], select[id$='alpha_receptor_blocker']").val();
    pcrData.abletolieflat = $eyeSide.find("select[id$='abletolieflat'], select[id$='can_lie_flat']").val();
    pcrData.doctorgrade = $eyeSide.find("select[id$='doctor_grade_id']").find(":selected").data('pcrValue');

    return pcrData;
}

/**
 *
 * @param inputValues
 * @returns {*}
 */
function calculateORValue(inputValues) {
    if (Object.keys(inputValues).length === 0) {
        return 0;
    }

    var OR = {};
    var orMultiplied = 1;  // base value

    // multipliers for the attributes and selected values
    OR.age = {'1': 1, '2': 1.14, '3': 1.42, '4': 1.58, '5': 2.37};
    OR.gender = {'Male': 1.28, 'Female': 1, 'Other': 1.14, 'Unknown': 1.14};
    OR.glaucoma = {'Y': 1.30, 'N': 1, 'NK': 1};
    OR.diabetic = {'Y': 1.63, 'N': 1, 'NK': 1};
    OR.fundalview = {'Y': 2.46, 'N': 1, 'NK': 1};
    OR.brunescentwhitecataract = {'Y': 2.99, 'N': 1, 'NK': 1};
    OR.pxf = {'Y': 2.92, 'N': 1, 'NK': 1};
    OR.pupilsize = {'Small': 1.45, 'Medium': 1.14, 'Large': 1, 'NK': 1};
    OR.axiallength = {'0': 1 ,'NK': 1, '1': 1, '2': 1.47};
    OR.alphareceptorblocker = {'Y': 1.51, 'N': 1, 'NK': 1};
    OR.abletolieflat = {'Y': 1, 'N': 1.27};
    OR.doctorgrade = {};

    if (Object.keys(inputValues).length !== Object.keys(OR).length) {
        return 0;
    }

    for (var key in inputValues) {
        if (!inputValues.hasOwnProperty(key)) {
            continue;
        }
        var riskFactor;
        //if we have a key to factor relationship use that, otherwise the factor is in the input value itself.
        if (OR[key].hasOwnProperty(inputValues[key])) {
            riskFactor = OR[key][inputValues[key]];
        } else {
            riskFactor = inputValues[key];
        }
        orMultiplied *= riskFactor;
    }

    return orMultiplied;
}

/**
 * Calculates the value
 */
function calculatePcrValue(ORValue) {
  var averageRiskConst,
    pcrRisk,
    excessRisk,
    pcrColour,
    textColour;

  if (ORValue) {
    pcrRisk = ORValue * (0.00736 / (1 - 0.00736)) / (1 + (ORValue * 0.00736 / (1 - 0.00736))) * 100;
    averageRiskConst = 1.92;
    excessRisk = pcrRisk / averageRiskConst;
    excessRisk = excessRisk.toFixed(2);
    pcrRisk = pcrRisk.toFixed(2);

    if (pcrRisk <= 1) {
      pcrColour = 'green';
      textColour = 'white';
    } else if (pcrRisk > 1 && pcrRisk <= 5) {
      pcrColour = 'orange';
      textColour = 'black';
    } else {
      pcrColour = 'red';
      textColour = 'white';
    }
  } else {
    pcrRisk = "N/A";
    excessRisk = "N/A";
    pcrColour = 'blue';
    textColour = 'white';
  }

  return {
    pcrRisk: pcrRisk,
    excessRisk: excessRisk,
    pcrColour: pcrColour,
    textColour: textColour
  };
}

/**
 * Calculates the PCR risk for a given side
 *
 * @param $eyeSide
 * @param side
 */
function pcrCalculate($eyeSide, side) {
    var pcrDataValues = collectValues($eyeSide),
        ORValue = calculateORValue(pcrDataValues),
        pcrData;

    side = capitalizeFirstLetter(side);
    pcrData = calculatePcrValue(ORValue);

  $eyeSide.find('.pcr-span').html(pcrData.pcrRisk);
  $eyeSide.find('.pcr-erisk').html(pcrData.excessRisk);
  if(pcrData.pcrRisk !== 'N/A'){
    $eyeSide.find('.pcr-input').val(pcrData.pcrRisk);
  } else {
    $eyeSide.find('.pcr-input').val('');
  }
  $eyeSide.find('.pcr-erisk-input').val(pcrData.excessRisk);

    $('#ophCiExaminationPCRRisk' + side + 'EyeLabel').find('a').css('color', pcrData.pcrColour);
    $('#ophCiExaminationPCRRisk' + side + 'EyeLabel').find('.pcr-span1').html(pcrData.pcrRisk);

    $('#ophCiExaminationPCRRiskEyeLabel').find('a').css('color', pcrData.pcrColour);
    $('#ophCiExaminationPCRRiskEyeLabel').find('.pcr-span1').html(pcrData.pcrRisk);
    //$('#ophCiExaminationPCRRisk'+side+'EyeLabel').find('.pcr-span1').css('color', pcrColor);
    if (pcrData.pcrRisk !== 'N/A') {
        $('#Element_OphTrOperationnote_Cataract_pcr_risk').val(pcrData.pcrRisk);
    }
}