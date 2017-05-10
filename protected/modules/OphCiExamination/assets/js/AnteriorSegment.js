var OpenEyes = OpenEyes || {};

OpenEyes.OphCiExamination = OpenEyes.OphCiExamination || {};

OpenEyes.OphCiExamination.AnteriorSegmentController = (function (ED) {
    /**
     *
     * @param options
     * @constructor
     */
    function AnteriorSegmentController(options)
    {
        this.options = $.extend(true, {}, AnteriorSegmentController._defaultOptions, options);
        this.initialise();
    }

    /**
     * Data structure containing all the configuration options for the controller
     * N.B. parameter syncing between the eyedraws is taken care of in standard OEEyedraw
     * sync array config
     *
     * @private
     */
    AnteriorSegmentController._defaultOptions = {
        // pairing of doodles from primary to secondary (cross section) eyedraw canvas
        pairArray: {
            Lens:'LensCrossSection',
            PCIOL:'PCIOLCrossSection',
            ACIOL: 'ACIOLCrossSection',
            CornealOpacity: 'CornealOpacityCrossSection'
        }
    };

    /**
     * Assign the primary drawing canvas to the controller
     *
     * @param drawing
     */
    AnteriorSegmentController.prototype.setPrimary = function(drawing)
    {
        if (!this.primaryDrawing) {
            this.primaryDrawing = drawing;
            this.primaryDrawing.registerForNotifications(this, 'primaryDrawingNotification', ['doodlesLoaded', 'doodleSelected', 'doodleAdded', 'doodleDeleted', 'parameterChanged']);
        }
    };

    /**
     * Assign the secondary (cross section) canvas to the controller
     *
     * @param drawing
     */
    AnteriorSegmentController.prototype.setSecondary = function(drawing)
    {
        this.secondaryDrawing = drawing;
        this.secondaryDrawing.registerForNotifications(this, 'secondaryDrawingNotification', ['doodlesLoaded', 'doodleSelected']);
    };

    /**
     * Set up any internal references needed by the controller
     */
    AnteriorSegmentController.prototype.initialise = function()
    {
        // Cache references to the form elements that will be affected by changes to the Eyedraws
        this.$edReportField = $('#OEModule_OphCiExamination_models_Element_OphCiExamination_AnteriorSegment_' + this.options.side + '_ed_report');
        this.$edReportDisplay = $('#OEModule_OphCiExamination_models_Element_OphCiExamination_AnteriorSegment_'+this.options.side+'_ed_report_display');
        this.$nuclearCataract = $('#OEModule_OphCiExamination_models_Element_OphCiExamination_AnteriorSegment_'+this.options.side+'_nuclear_id');
        this.$corticalCataract = $('#OEModule_OphCiExamination_models_Element_OphCiExamination_AnteriorSegment_'+this.options.side+'_cortical_id');

    };

    /**
     * Check if the primary canvas is ready
     *
     * @returns boolean
     */
    AnteriorSegmentController.prototype.primaryDrawingReady = function()
    {
        return this.primaryDrawing && this.primaryDrawing.isReady;
    };

    /**
     * Check if the secondary canvas is ready
     *
     * @returns boolean
     */
    AnteriorSegmentController.prototype.secondaryDrawingReady = function()
    {
        return this.secondaryDrawing && this.secondaryDrawing.isReady;
    };

    /**
     * Update the display div of the eyedraw report
     */
    AnteriorSegmentController.prototype.updateReport = function()
    {
        this.$edReportDisplay.html(this.$edReportField.val().replace(/\n/g,'<br />'));
    };

    /**
     * Store the given value to the provided input element (syncing of data items that need to
     * be recorded against the element (and therefore in the backend db) for reporting/analysis)
     *
     * @param $el
     * @param lookupValue
     */
    AnteriorSegmentController.prototype.storeToHiddenField = function($el, lookupValue)
    {
        var map = $el.data('eyedraw-map');
        if (lookupValue in map) {
            $el.val(map[lookupValue]);
        } else {
            $el.val($el.data('eyedraw-default'));
        }
        $el.trigger('change');
    };

    /**
     * Handler of notifications from the primary Eyedraw canvas
     * All changes require the report to be updated, but additional behaviours also arise
     * depending on the notification type.
     *
     * @param msgArray
     */
    AnteriorSegmentController.prototype.primaryDrawingNotification = function(msgArray)
    {
        switch (msgArray['eventName'])
        {
        case 'doodlesLoaded':
            this.updateReport();
            break;

        case 'doodleSelected':
            // Ensure that selecting a doodle in one drawing de-deselects the others
            if (this.secondaryDrawingReady()) {
                this.secondaryDrawing.deselectDoodles();

            }
            this.updateReport();
            break;

        case 'doodleAdded':

            // Newly added doodle is passed in message object
            var newDoodle = msgArray['object'];
            
            // Remove biological lens if IOL inserted
            if (newDoodle.className == 'ACIOL' || newDoodle.className == 'PCIOL') {
                var biologicalLens = newDoodle.drawing.lastDoodleOfClass('Lens');
                if (biologicalLens) {
                    newDoodle.drawing.deleteDoodlesOfClass(biologicalLens.className);
                }
            }

            // Check pair array for doodle to add to secondary
            if (this.secondaryDrawingReady()) {
                for (var primaryClass in this.options.pairArray) {
                    if (newDoodle.className == primaryClass) {
                        var secondaryClass = this.options.pairArray[primaryClass];
                        if (!this.secondaryDrawing.hasDoodleOfClass(secondaryClass)) {
                            this.secondaryDrawing.addDoodle(secondaryClass);
                            this.secondaryDrawing.deselectDoodles();
                        }
                    }
                }
            }

            if (newDoodle.className === 'Lens') {
                // ensure the initial value is stored correctly.
                this.storeToHiddenField(this.$nuclearCataract, newDoodle.nuclearGrade);
                this.storeToHiddenField(this.$corticalCataract, newDoodle.corticalGrade);
            }
            this.updateReport();
            break;
        case 'doodleDeleted':

            // Class of deleted doodle
            var deletedDoodleClass = msgArray['object'];
            // check if a pair doodle should be removed from secondary
            if (this.secondaryDrawingReady()) {
                for (var primaryClass in this.options.pairArray) {
                    if (deletedDoodleClass == primaryClass) {

                        var secondaryClass = this.options.pairArray[primaryClass];
                        if (this.secondaryDrawing.hasDoodleOfClass(secondaryClass)) {
                            this.secondaryDrawing.deleteDoodlesOfClass(secondaryClass);
                            this.secondaryDrawing.deselectDoodles();
                        }
                    }
                }
            }

            if (deletedDoodleClass === 'Lens') {
                // reset to the null value as this removes any cataract value
                this.storeToHiddenField(this.$nuclearCataract, '');
                this.storeToHiddenField(this.$corticalCataract, '');
            }
            this.updateReport();
            break;
        case 'parameterChanged':
            this.updateReport();
            var change = msgArray['object'];
            if (change.doodle.className === 'Lens') {
                if (change.parameter === 'nuclearGrade') {
                    this.storeToHiddenField(this.$nuclearCataract, change.value);
                }
                if (change.parameter === 'corticalGrade') {
                    this.storeToHiddenField(this.$corticalCataract, change.value);
                }
            }
            break;
        }
    };

    /**
     * Handle secondary drawing notifications
     *
     * @param msgArray
     */
    AnteriorSegmentController.prototype.secondaryDrawingNotification = function(msgArray)
    {
        switch (msgArray['eventName']) {
            case 'doodlesLoaded':
                break;
            case 'doodleSelected':
                if (this.primaryDrawingReady())
                    this.primaryDrawing.deselectDoodles();
                break;
        }
    };

    return AnteriorSegmentController;
})(ED);

function anteriorSegmentListener(_drawing)
{
    var canvas = $(_drawing.canvas);
    var drawingId = $(_drawing.canvas).attr('id');
    var secondary = drawingId.endsWith('_side');
    if (secondary) {
        canvas = $('#' + drawingId.substring(0, drawingId.length - 5));
    }
    var controller = canvas.data('controller');
    if (!controller) {
        controller = new OpenEyes.OphCiExamination.AnteriorSegmentController(
          {side: (_drawing.eye === 1 ? 'left' : 'right')}
        );
        canvas.data('controller', controller);
    }
    if (secondary) {
        controller.setSecondary(_drawing);
    } else {
        controller.setPrimary(_drawing);
    }
}
