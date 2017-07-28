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
            AntSeg: 'AntSegCrossSection',
            Cornea: 'CorneaCrossSection',
            PCIOL:'PCIOLCrossSection',
            ACIOL: 'ACIOLCrossSection',
            CornealOpacity: 'CornealOpacityCrossSection',
            Hypopyon: 'HypopyonCrossSection',
            Hyphaema: 'HyphaemaCrossSection'
        },
        // Ideally this knowledge would be baked into the enface doodle classes
        // but with only one instance of this behaviour and time constraints, this
        // is the simpler implementation.
        secondarySyncParams: {
            AntSegCrossSection: {
                primaryDoodleClass: 'AntSeg',
                parameters: {apexX: 'csApexX'}
            },
            LensCrossSection: {
                primaryDoodleClass: 'Lens',
                parameters: {originX: 'csOriginX'}
            },
            CorneaCrossSection: {
                primaryDoodleClass: 'Cornea',
                parameters: {
                    shape: 'shape',
                    pachymetry: 'pachymetry',
                    originX: 'csOriginX',
                    apexX: 'csApexX',
                    apexY: 'csApexY'
                }
            },
            PCIOLCrossSection: {
                primaryDoodleClass: 'PCIOL',
                parameters: {originX: 'csOriginX', originY: 'originY'}
            },
            ACIOLCrossSection: {
                primaryDoodleClass: 'ACIOL',
                parameters: {originX: 'csOriginX'}
            }
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
            this.primaryDrawing.registerForNotifications(this, 'primaryDrawingNotification', ['ready', 'doodlesLoaded', 'doodleSelected', 'doodleAdded', 'doodleDeleted', 'parameterChanged']);
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
        this.secondaryDrawing.registerForNotifications(this, 'secondaryDrawingNotification', ['ready', 'parameterChanged', 'doodlesLoaded', 'doodleSelected']);
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
        this.secondaryDoodlesLoaded = false;
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
     * Drives the loading of the cross section doodles from the doodles in the primary (enface) view
     */
    AnteriorSegmentController.prototype.loadSecondaryDoodles = function()
    {
        if (!this.secondaryDoodlesLoaded) {
            for (var i = 0; i < this.primaryDrawing.doodleArray.length; i++) {
                var doodle = this.primaryDrawing.doodleArray[i];
                // check it's a doodle that we want to pair into the cross section
                if (this.options.pairArray.hasOwnProperty(doodle.className)) {
                    var csClass = this.options.pairArray[doodle.className];
                    parameters = {};
                    // look for parameters we want to set from primary.
                    if (this.options.secondarySyncParams.hasOwnProperty(csClass)) {
                        // retrieve the cs based parameters that we need to set up
                        var conf = this.options.secondarySyncParams[csClass];
                        for (var param in conf.parameters) {
                            if (conf.parameters.hasOwnProperty(param)) {
                                parameters[param] = doodle[conf.parameters[param]];
                            }
                        }
                    }
                    // should probably check this earlier, and may want to iterate over parameters instead?
                    if (!this.secondaryDrawing.hasDoodleOfClass(csClass)) {
                        this.secondaryDrawing.addDoodle(csClass, parameters);
                    }
                }

            }
            this.secondaryDoodlesLoaded = true;
            this.secondaryDrawing.deselectDoodles();
        }
    };

    /**
     * Syncing parameters back to the enface doodles that are not "naturally" synced
     *
     * @param edClass - the class of doodle on the primary drawing
     * @param parameterName - the parameter name on the primary doodle that should be updated
     * @param changedParameter - the changedParameter object from Eyedraw
     */
    AnteriorSegmentController.prototype.updatePrimaryParameter = function(edClass, parameterName, changedParameter)
    {
        var primaryDoodle = this.primaryDrawing.firstDoodleOfClass(edClass);
        // avoid infinite loop of continually updating the parameter through notifications
        if (primaryDoodle[parameterName] == changedParameter.value)
            return;

        if (typeof(changedParameter.value) === "string") {
            primaryDoodle.setParameterFromString(parameterName, changedParameter.value, true);
        } else {
            var increment = changedParameter.value - changedParameter.oldValue;
            var newValue = primaryDoodle[parameterName] + increment;

            // Sync slave parameter to value of master
            primaryDoodle.setSimpleParameter(parameterName, newValue);
            primaryDoodle.updateDependentParameters(parameterName);
        }
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
        case 'ready':
            if (this.secondaryDrawingReady()) {
                this.loadSecondaryDoodles();
            }
            break;

        case 'doodleSelected':
            // Ensure that selecting a doodle in one drawing de-deselects the others
            if (this.secondaryDrawingReady()) {
                this.secondaryDrawing.deselectDoodles();
            }
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
            if (this.secondaryDrawingReady() && this.secondaryDoodlesLoaded) {
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
            break;
        case 'parameterChanged':
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
            case 'ready':
                if (this.primaryDrawingReady())
                    this.loadSecondaryDoodles();
                break;
            case 'doodlesLoaded':
                break;
            case 'doodleSelected':
                if (this.primaryDrawingReady())
                    this.primaryDrawing.deselectDoodles();
                break;
            case 'parameterChanged':
                if (this.secondaryDoodlesLoaded) {
                    var change = msgArray['object'];
                    for (var className in this.options.secondarySyncParams) {
                        if (this.options.secondarySyncParams.hasOwnProperty(className) &&
                            change.doodle.className == className
                        ) {
                            var conf = this.options.secondarySyncParams[className];
                            var parameters = conf['parameters'];
                            for (var param in parameters) {
                                if (parameters.hasOwnProperty(param) && change.parameter === param) {
                                    this.updatePrimaryParameter(conf['primaryDoodleClass'], parameters[param], change);
                                }
                            }
                        }
                    }
                }
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
