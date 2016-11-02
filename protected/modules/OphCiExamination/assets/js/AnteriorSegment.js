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
            this.primaryDrawing.registerForNotifications(this, 'primaryDrawingNotification', ['doodlesLoaded', 'doodleSelected', 'doodleAdded', 'doodleDeleted']);
        }
    };

    AnteriorSegmentController.prototype.setSecondary = function(drawing)
    {
        this.secondaryDrawing = drawing;
        this.secondaryDrawing.registerForNotifications(this, 'secondaryDrawingNotification', ['doodlesLoaded', 'doodleSelected']);
    };

    AnteriorSegmentController.prototype.initialise = function()
    {
        //
    };

    AnteriorSegmentController.prototype.primaryDrawingReady = function()
    {
        return this.primaryDrawing && this.primaryDrawing.isReady;
    };

    AnteriorSegmentController.prototype.secondaryDrawingReady = function()
    {
        return this.secondaryDrawing && this.secondaryDrawing.isReady;
    };

    AnteriorSegmentController.prototype.primaryDrawingNotification = function(msgArray)
    {
        switch (msgArray['eventName'])
        {
        case 'doodlesLoaded':
            console.log('primary loaded');
            this.primaryDrawingReady = true;
            break;

        case 'doodleSelected':
            // Ensure that selecting a doodle in one drawing de-deselects the others
            if (this.secondaryDrawingReady())
                this.secondaryDrawing.deselectDoodles();
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

            // Adjust position in relation to other doodles (array defined in the doodle subclass)
            for (var i = 0; i < newDoodle.inFrontOfClassArray.length; i++)
            {
                newDoodle.drawing.moveNextTo(newDoodle, newDoodle.inFrontOfClassArray[i], true)
            }

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

            break;

        }
    };

    AnteriorSegmentController.prototype.secondaryDrawingNotification = function(msgArray)
    {
        switch (msgArray['eventName']) {
            case 'doodlesLoaded':
                console.log('secondary loaded');
                this.secondaryDrawingReady = true;
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
        controller = new OpenEyes.OphCiExamination.AnteriorSegmentController();
        canvas.data('controller', controller);
    }
    if (secondary) {
        controller.setSecondary(_drawing);
    } else {
        controller.setPrimary(_drawing);
    }
}
