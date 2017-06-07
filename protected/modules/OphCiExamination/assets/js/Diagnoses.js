var OpenEyes = OpenEyes || {};

OpenEyes.OphCiExamination = OpenEyes.OphCiExamination || {};

OpenEyes.OphCiExamination.DiagnosesController = (function (ED) {

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
        // will want to send a signal telling anything interested to provide us with external disorders.
    };

    var externalDiagnoses = {};

    DiagnosesController.prototype.setExternalDiagnoses = function(diagnosesBySource)
    {
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

        externalDiagnoses = newExternalDiagnoses;
        this.renderExternalDiagnoses();
    };

    DiagnosesController.prototype.renderExternalDiagnoses = function()
    {
        for (diagnosisCode in externalDiagnoses) {
            if (externalDiagnoses.hasOwnProperty(diagnosisCode)) {
                this.updateDiagnosis(diagnosisCode, externalDiagnoses[diagnosisCode].sides);
            }
        }
    };

    DiagnosesController.prototype.updateDiagnosis = function(code, sides)
    {
        var self = this;
        // TODO: the callback function obviously should be moved into this controller
        self.retrieveDiagnosisDetail(code, self.resolveEyeCode(sides), self.setDiagnosis);
    };

    var diagnosisDetail = {};

    DiagnosesController.prototype.retrieveDiagnosisDetail = function(code, sides, callback)
    {
        if (diagnosisDetail.hasOwnProperty(code)) {
            callback(diagnosisDetail[code].id, diagnosisDetail[code].name, sides);
        } else {
            $.ajax({
                'type': 'GET',
                // TODO: this should be a property of the element
                'url': '/OphCiExamination/default/getDisorder?disorder_id='+code,
                'success': function(json) {
                    diagnosisDetail[code] = json;
                    callback(diagnosisDetail[code].id, diagnosisDetail[code].name, sides);
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
     * Check for the diagnosis in the current diagnosis element. If it's there, update the side.
     * If it's not, add it to the table.
     *
     * @param id
     * @param name
     * @param side
     */
    DiagnosesController.prototype.setDiagnosis = function(id, name, side)
    {
        // code adapted from module.js to verify if diagnosis already in table or not
        var alreadyInList = false;
        var listSide = null;

        // iterate over table rows.
        $('#OphCiExamination_diagnoses').children('tr').each(function() {
            if ($(this).find('input[name=selected_diagnoses\\[\\]]').val() == id) {
                alreadyInList = true;
                listSide = $('input[type="radio"]:checked').val();
                if (listSide != side) {
                    // the
                    $(this).find('input[type="radio"][value='+side+']').prop('checked', true);
                }
                // stop iterating
                return false;
            }
        });

        if (!alreadyInList) {
            // TODO: this should be a method on this controller, but we're leveraging existing code for now.
            OphCiExamination_AddDiagnosis(id, name, side);
        }
    };

    return DiagnosesController;
})();

$(document).ready(function() {
    $('#OphCiExamination_diagnoses').data('controller', new OpenEyes.OphCiExamination.DiagnosesController());
});