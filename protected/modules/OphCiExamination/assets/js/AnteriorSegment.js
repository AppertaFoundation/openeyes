var OpenEyes = OpenEyes || {};

OpenEyes.OphCiExamination = OpenEyes.OphCiExamination || {};

OpenEyes.OphCiExamination.AnteriorSegmentController = (function (ED) {
    function AnteriorSegmentController(options)
    {
        this.options = $.extend(true, {}, AnteriorSegmentController._defaultOptions, options);
        this.initialise();
    }

    AnteriorSegmentController._defaultOptions = {
        pairArray: {
            Lens:'LensCrossSection',
            PCIOL:'PCIOLCrossSection',
            ACIOL: 'ACIOLCrossSection',
            CornealOpacity: 'CornealOpacityCrossSection'
        },
        syncArray: {
            primary: {
                other: {
                    AntSeg: { AntSegCrossSection: {parameters:['apexY']} },
                    Lens: {
                        LensCrossSection: { parameters: ['originY', 'nuclearGrade', 'corticalGrade', 'posteriorSubcapsularGrade'] }
                    },
                    ACIOL: { ACIOLCrossSection: { parameters: ['originY'] } },
                    PCIOL: { PCIOLCrossSection: { parameters: ['originY', 'fx'] } },
                    NuclearCataract: { NuclearCataractCrossSection: { parameters: ['apexY'] } },
                    CorticalCataract: { CorticalCataractCrossSection: { parameters:['apexY'] } },
                    PostSubcapCataract: { PostSubcapCataractCrossSection: { parameters:['apexY'] } },
                    CornealOpacity: { CornealOpacityCrossSection: {parameters: ['yMidPoint','d','h','w','iW','originY'] } }
                },
                own: {
                    Lens:{
                        NuclearCataract:{parameters:['originX', 'originY']},
                        CorticalCataract:{parameters:['originX', 'originY']},
                        PostSubcapCataract:{parameters:['originX', 'originY']},
                    },
                    NuclearCataract:{Lens:{parameters:['originX', 'originY']}},
                    CorticalCataract:{Lens:{parameters:['originX', 'originY']}},
                }

            },
            secondary: {
                other: {
                    AntSegCrossSection: {AntSeg: {parameters: ['apexY']}},
                    LensCrossSection: {Lens: {parameters: ['originY']}},
                    ACIOLCrossSection: {ACIOL: {parameters: ['originY']}},
                    PCIOLCrossSection: {PCIOL: {parameters: ['originY', 'fx']}},
                    NuclearCataractCrossSection: {NuclearCataract: {parameters: ['apexY']}},
                    CorticalCataractCrossSection: {CorticalCataract: {parameters: ['apexY']}},
                    CornealOpacityCrossSection: {CornealOpacity: {parameters: ['yMidPoint', 'd', 'h', 'w', 'iW']}}
                },
                own: {
                    LensCrossSection:{
                        NuclearCataractCrossSection:{parameters:['originX', 'originY']},
                        CorticalCataractCrossSection:{parameters:['originX', 'originY']},
                        PostSubcapCataractCrossSection:{parameters:['originX', 'originY']}
                    },
                    NuclearCataractCrossSection:{LensCrossSection:{parameters:['originX', 'originY']}},
                    CorticalCataractCrossSection:{LensCrossSection:{parameters:['originX', 'originY']}},
                }
            }
        }
    };

    AnteriorSegmentController.prototype.setPrimary = function(drawing)
    {
        if (!this.primaryDrawing) {
            this.primaryDrawing = drawing;
            this.primaryDrawing.registerForNotifications(this, 'primaryDrawingNotification', ['doodlesLoaded', 'doodleSelected', 'doodleAdded', 'doodleDeleted', 'parameterChanged']);
        }
    };

    AnteriorSegmentController.prototype.setSecondary = function(drawing)
    {
        this.secondaryDrawing = drawing;
        this.secondaryDrawing.registerForNotifications(this, 'secondaryDrawingNotification', ['doodlesLoaded', 'doodleSelected']);
    };

    AnteriorSegmentController.prototype.initialise = function()
    {
        this.$edReportField = $('#OEModule_OphCiExamination_models_Element_OphCiExamination_AnteriorSegment_' + this.options.side + '_ed_report');
        this.$edReportDisplay = $('#OEModule_OphCiExamination_models_Element_OphCiExamination_AnteriorSegment_'+this.options.side+'_ed_report_display');
        this.$nuclearCataract = $('#OEModule_OphCiExamination_models_Element_OphCiExamination_AnteriorSegment_'+this.options.side+'_nuclear_id');

    };

    AnteriorSegmentController.prototype.primaryDrawingReady = function()
    {
        return this.primaryDrawing && this.primaryDrawing.isReady;
    };

    AnteriorSegmentController.prototype.secondaryDrawingReady = function()
    {
        return this.secondaryDrawing && this.secondaryDrawing.isReady;
    };

    AnteriorSegmentController.prototype.updateReport = function()
    {
        this.$edReportDisplay.html(this.$edReportField.val().replace(/\n/g,'<br />'));
    };

    AnteriorSegmentController.prototype.storeToHiddenField = function($el, lookupValue)
    {
        var map = $el.data('eyedraw-map');
        var value = '';
        if (lookupValue in map) {
            $el.val(map[lookupValue]);
        } else {
            $el.val($el.data('eyedraw-default'));
        }
    };

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

            // Check pair array
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
            this.updateReport();
            break;
        case 'doodleDeleted':

            // Class of deleted doodle
            var deletedDoodleClass = msgArray['object'];
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
            this.updateReport();
            break;
            case 'parameterChanged':
            this.updateReport();
            var change = msgArray['object'];
            if (change.doodle.className === 'Lens') {
                // TODO: map for cortical as well as nuclear
                if (change.parameter === 'nuclearGrade') {
                    this.storeToHiddenField(this.$nuclearCataract, change.value);
                }
            }
            break;
        }
    };

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
