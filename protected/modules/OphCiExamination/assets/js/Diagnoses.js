var OpenEyes = OpenEyes || {};

OpenEyes.OphCiExamination = OpenEyes.OphCiExamination || {};

OpenEyes.OphCiExamination.DiagnosesController = (function () {

    /**
     *
     * @param options
     * @constructor
     */
    function DiagnosesController(options)
    {
        this.options = $.extend(true, {}, DiagnosesController._defaultOptions, options);
        this.initialise();
    }

    /**
     * Data structure containing all the configuration options for the controller
     * @private
     */
    DiagnosesController._defaultOptions = {
        'selector': '#OphCiExamination_diagnoses'
    };

    DiagnosesController.prototype.initialise = function()
    {
        // possibly load up current disorders and cache them for later comparison checking.
        this.$element = $(this.options.selector);
    };

    var externalDiagnoses = {};

    /**
     * Set the external diagnoses and update the element display accordingly.
     *
     * @param diagnosesBySource
     */
    DiagnosesController.prototype.setExternalDiagnoses = function(diagnosesBySource)
    {
        // reformat to controller usable structure
        var newExternalDiagnoses = {};
        for (var source in diagnosesBySource) {
            if (diagnosesBySource.hasOwnProperty(source)) {
                for (var i = 0; i < diagnosesBySource[source].length; i++) {
                    var diagnosis = diagnosesBySource[source][i][0];
                    if (diagnosesBySource[source][i][0] in newExternalDiagnoses) {
                        if (!(diagnosesBySource[source][i][1] in newExternalDiagnoses)) {
                            newExternalDiagnoses[diagnosis].sides.push(diagnosesBySource[source][i][1]);
                        }
                    } else {
                        newExternalDiagnoses[diagnosis] = {sides: [diagnosesBySource[source][i][1]]}
                    }
                }
            }
        }

        // check for external diagnoses that should be removed
        for (var code in externalDiagnoses) {
            if (externalDiagnoses.hasOwnProperty(code)) {
                if (!(code in newExternalDiagnoses)) {
                    this.removeExternalDiagnosis(code);
                }
            }
        }

        // assign private property
        externalDiagnoses = newExternalDiagnoses;
        // update display
        this.renderExternalDiagnoses();
    };

    /**
     * Remove the diagnosis if it was added from an external source.
     */
    DiagnosesController.prototype.removeExternalDiagnosis = function(code)
    {
        this.$element.find('.external a.removeDiagnosis[rel="'+code+'"]').click();
    };

    /**
     * Runs through the current external diagnoses and ensures they are displayed correctly
     */
    DiagnosesController.prototype.renderExternalDiagnoses = function()
    {
        for (diagnosisCode in externalDiagnoses) {
            if (externalDiagnoses.hasOwnProperty(diagnosisCode)) {
                this.updateExternalDiagnosis(diagnosisCode, externalDiagnoses[diagnosisCode].sides);
            }
        }
    };

    /**
     * Update the given diagnosis to apply to sides
     *
     * @param code
     * @param sides
     */
    DiagnosesController.prototype.updateExternalDiagnosis = function(code, sides)
    {
        var self = this;
        self.retrieveDiagnosisDetail(code, self.resolveEyeCode(sides), self.setExternalDiagnosisDisplay);
    };

    var diagnosisDetail = {};

    /**
     * This will retrieve the diagnosis detail via ajax (if it's not already been retrieved)
     * and then pass the information to the given callback. The callback function should expect
     * to receive args [diagnosisId, diagnosisName, sideId]
     * @param code
     * @param sides
     * @param callback
     */
    DiagnosesController.prototype.retrieveDiagnosisDetail = function(code, side, callback)
    {
        if (diagnosisDetail.hasOwnProperty(code)) {
            callback(diagnosisDetail[code].id, diagnosisDetail[code].name, side);
        } else {
            $.ajax({
                'type': 'GET',
                // TODO: this should be a property of the element
                'url': '/OphCiExamination/default/getDisorder?disorder_id='+code,
                'success': function(json) {
                    diagnosisDetail[code] = json;
                    callback(diagnosisDetail[code].id, diagnosisDetail[code].name, side);
                }
            });
        }
    };

    /**
     * Expecting array of side values where 0 is right and 1 is left
     * If both are present returns 3 (both)
     * otherwise returns 2 for right and 1 for left
     * or undefined if no meaningful value is provided
     *
     * @param sides
     */
    DiagnosesController.prototype.resolveEyeCode = function(sides)
    {
        var left = false;
        var right = false;
        for (var i = 0; i < sides.length; i++) {
            if (sides[i] === 0)
                right = true;
            if (sides[i] === 1)
                left = true;
        }

        return right ? (left ? 3 : 2) : (left ? 1 : undefined);
    };

    /**
     * Check for the diagnosis in the current diagnosis element. If it's there, and is external, update the side.
     * If it's not, add it to the table.
     *
     * @param id
     * @param name
     * @param side
     */
    DiagnosesController.prototype.setExternalDiagnosisDisplay = function(id, name, side)
    {
        // code adapted from module.js to verify if diagnosis already in table or not
        var alreadyInList = false;
        var listSide = null;

        // iterate over table rows.
        $('#OphCiExamination_diagnoses').children('tr').each(function() {
            if ($(this).find('input[name=selected_diagnoses\\[\\]]').val() == id) {
                alreadyInList = true;
                if ($(this).hasClass('external')) {
                    // only want to alter sides for disorders that have been added from external source
                    // at this point
                    listSide = $('input[type="radio"]:checked').val();
                    if (listSide != side) {
                        // the
                        $(this).find('input[type="radio"][value=' + side + ']').prop('checked', true);
                    }
                }
                // stop iterating
                return false;
            }
        });

        if (!alreadyInList) {
            // TODO: this should be a method on this controller, but we're leveraging existing code for now.
            // NOTE: the hardcoded settings for diabetic/glaucoma flags are present to allow us to provide
            // the auto flag for control of removing diagnoses as the eyedraws are updated
            OphCiExamination_AddDiagnosis(id, name, side, false, false, true);
        }
    };

    return DiagnosesController;
})();

$(document).ready(function() {
    $('#OphCiExamination_diagnoses').data('controller', new OpenEyes.OphCiExamination.DiagnosesController());
    // would be better to do this from within the controller via a signal, but this a quick solution
    OpenEyes.OphCiExamination.Diagnosis.sync();
});