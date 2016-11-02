var OpenEyes = OpenEyes || {};

OpenEyes.OphCiExamination = OpenEyes.OphCiExamination || {};

OpenEyes.OphCiExamination.AnteriorSegmentController = (function (ED) {
    function AnteriorSegmentController(drawing, options)
    {
        this.primaryDrawing = drawing;
        this.options = $.extend(true, {}, AnteriorSegmentController._defaultOptions, options);

        this.initialise();
    }

    AnteriorSegmentController._defaultOptions = {
        pairArray: {
            Lens:'LensCrossSection',
            CorticalCataract:'CorticalCataractCrossSection',
            NuclearCataract:'NuclearCataractCrossSection',
            PostSubcapCataract:'PostSubcapCataractCrossSection',
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

    AnteriorSegmentController.prototype.initialise = function()
    {
        if (this.options.secondarySelector === undefined) {
            this.options.secondarySelector = '#' + $(this.primaryDrawing.canvas).attr('id') + '_side';
        }
        if (!$(this.options.secondarySelector)) {
            console.log('ERROR: Could not find secondary canvas with selector ' + this.options.secondarySelector);
        }

        this.secondaryDrawing = new ED.Drawing($(this.options.secondarySelector)[0],
            this.primaryDrawing.eye,
            this.primaryDrawing.idSuffix + '_side',
            true,
            {graphicsPath: this.primaryDrawing.graphicsPath});

        this.secondaryDrawing.init();

        this.linkDrawings();
    };

    AnteriorSegmentController.prototype.linkDrawings = function()
    {
        this.primaryDrawing.registerForNotifications(this, 'primaryDrawingNotification', []);
        this.secondaryDrawing.registerForNotifications(this, 'secondaryDrawingNotification', []);
    };

    AnteriorSegmentController.prototype.primaryDrawingNotification = function(msgArray)
    {
        switch (msgArray['eventName'])
        {
        case 'doodleSelected':

            // Ensure that selecting a doodle in one drawing de-deselects the others
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
            for (var primaryClass in this.options.pairArray) {
                if (newDoodle.className == primaryClass) {
                    var secondaryClass = this.options.pairArray[primaryClass];
                    if (!this.secondaryDrawing.hasDoodleOfClass(secondaryClass)) {
                        this.secondaryDrawing.addDoodle(secondaryClass);
                        this.secondaryDrawing.deselectDoodles();
                    }
                }
            }

            // Adjust position in relation to other doodles (array defined in the doodle subclass)
            for (var i = 0; i < newDoodle.inFrontOfClassArray.length; i++)
            {
                newDoodle.drawing.moveNextTo(newDoodle, newDoodle.inFrontOfClassArray[i], true)
            }

            // Sync with any parent doodle
            var parentDoodle = newDoodle.drawing.firstDoodleOfClass(newDoodle.parentClass);
            if (parentDoodle)
            {
                var syncWith = newDoodle.drawing == this.primaryDrawing ? this.options.syncArray.primary : this.options.syncArray.secondary;
                for (var className in syncWith)
                {
                    if (className == parentDoodle.className)
                    {
                        for (var syncClassName in syncWith[className])
                        {
                            if (syncClassName == newDoodle.className)
                            {
                                // Get array of parameters to sync
                                var parameterArray = syncWith[className][syncClassName]['parameters'];

                                if (typeof(parameterArray) != 'undefined')
                                {
                                    // Iterate through parameters to sync
                                    for (var i = 0; i < parameterArray.length; i++)
                                    {
                                        // Sync slave parameter to value of master
                                        newDoodle.setSimpleParameter(parameterArray[i], parentDoodle[parameterArray[i]]);
                                        newDoodle.updateDependentParameters(parameterArray[i]);

                                        // Update any bindings associated with the slave doodle
                                        newDoodle.drawing.updateBindings(newDoodle);
                                    }
                                }
                            }
                        }
                    }
                }
            }
            //addToReport();
            break;


        case 'doodleDeleted':

            // Class of deleted doodle
            var deletedDoodleClass = msgArray['object'];

            for (var primaryClass in this.options.pairArray) {
                if (deletedDoodleClass == primaryClass) {
                    var secondaryClass = this.options.pairArray[primaryClass];
                    if (this.secondaryDrawing.hasDoodleOfClass(secondaryClass)) {
                        this.secondaryDrawing.deleteDoodlesOfClass(secondaryClass);
                        this.secondaryDrawing.deselectDoodles();
                    }
                }
            }

            // Check pair array
            $(this.options.pairArray).each(function (className, syncClassName) {
                // Only consider array entries for newly added doodle
                if (deletedDoodleClass == className)
                {
                    // Check it exists
                    if (this.secondaryDrawing.hasDoodleOfClass(syncClassName))
                    {
                        this.secondaryDrawing.deleteDoodlesOfClass(syncClassName);
                    }
                }
                else if (deletedDoodleClass == syncClassName) {
                    if (this.primaryDrawing.hasDoodleOfClass(className))
                    {
                        this.primaryDrawing.deleteDoodlesOfClass(className);
                    }
                }
            });

            // Look for any doodles which are children of the deleted one
            //var drawing = (_drawing.idSuffix == 'RPS')?thisDrawing:otherDrawing;
            //for (var i = 0; i < drawing.doodleArray.length; i++)
            //{
            //    if (deletedDoodleClass == drawing.doodleArray[i].parentClass)
            //    {
            //        drawing.deleteDoodlesOfClass(drawing.doodleArray[i].className);
            //    }
            //}
            //addToReport();
            break;

        case 'parameterChanged':
            console.log(msgArray['object']);
            this.syncDoodle('primary', msgArray);
//                         	console.log(msgArray['object'].parameter);

            //// Master doodle
            //var masterDoodle = msgArray['object'].doodle;
            //
            //// Iterate through syncArray for each syncronised drawing
            //for (var idSuffix in syncArray)
            //{
            //    // Define which drawing is slave
            //    var slaveDrawing = idSuffix == 'RPS'?drawingEdit1:drawingEdit2;
            //
            //    // Iterate through each specified className doing syncronisation
            //    for (var className in syncArray[idSuffix])
            //    {
            //        // Iterate through slave class names
            //        for (var syncClassName in syncArray[idSuffix][className])
            //        {
            //            // Slave doodle (uses first doodle in the drawing matching the className)
            //            var syncDoodle = slaveDrawing.firstDoodleOfClass(syncClassName);
            //
            //            // Check that doodles exist, className matches, and sync is allowed
            //            if (masterDoodle && masterDoodle.className == className && syncDoodle && syncDoodle.willSync)
            //            {
            //                // Get array of parameters to sync
            //                var parameterArray = syncArray[idSuffix][className][syncClassName]['parameters'];
            //
            //                if (typeof(parameterArray) != 'undefined')
            //                {
            //                    // Iterate through parameters to sync
            //                    for (var i = 0; i < parameterArray.length; i++)
            //                    {
            //                        // Check that parameter array member matches changed parameter
            //                        if (parameterArray[i] == msgArray.object.parameter)
            //                        {
            //                            // Avoid infinite loop by checking values are not equal before setting
            //                            if (masterDoodle[msgArray.object.parameter] != syncDoodle[msgArray.object.parameter])
            //                            {
            //                                var increment = msgArray.object.value - msgArray.object.oldValue;
            //                                var newValue = syncDoodle[msgArray.object.parameter] + increment;
            //
            //                                // Sync slave parameter to value of master
            //                                syncDoodle.setSimpleParameter(msgArray.object.parameter, newValue);
            //                                syncDoodle.updateDependentParameters(msgArray.object.parameter);
            //
            //                                // Update any bindings associated with the slave doodle
            //                                slaveDrawing.updateBindings(syncDoodle);
            //                            }
            //                        }
            //                    }
            //                }
            //            }
            //        }
            //    }
            //
            //    // Refresh slave drawing
            //    slaveDrawing.repaint();
            //}
            //addToReport();
            break;
//        case 'mouseup':
////                         	drawingEdit2.firstDoodleOfClass('Corneal')
//            var selectedDoodle = msgArray.selectedDoodle;
//            if (selectedDoodle && selectedDoodle.className == 'CornealOpacity') {
//                var syncDoodle = drawingEdit2.firstDoodleOfClass('CornealOpacityCrossSection');
//                syncDoodle.h = selectedDoodle.h;
//                syncDoodle.height = selectedDoodle.height;
//                syncDoodle.w = selectedDoodle.w;
//                syncDoodle.width = selectedDoodle.width;
//                syncDoodle.yMidPoint = selectedDoodle.yMidPoint;
//                syncDoodle.drawing.repaint();
//            }
//            break;
        }
    };

    AnteriorSegmentController.prototype.secondaryDrawingNotification = function(msgArray)
    {
        switch (msgArray['eventName']) {
            case 'ready':
                this.secondaryDrawing.addDoodle('CorneaCrossSection');
                this.secondaryDrawing.addDoodle('AntSegCrossSection');
                this.secondaryDrawing.addDoodle('LensCrossSection');
                break;
            case 'doodleSelected':
                this.primaryDrawing.deselectDoodles();
                break;
            case 'doodleAdded':
                // nothing to do at the moment as all doodles should be added to primary.
                break;
            case 'doodleDeleted':
                // nothing to do at the moment as all doodles should be deleted on primary.
                break;
            case 'parameterChanged':
                this.syncDoodle('secondary', msgArray);
                break;
        }
    };

    AnteriorSegmentController.prototype.syncDoodle = function(source, msgArray)
    {
        var syncArray = this.options.syncArray[source];
        var drawing = (source == 'primary') ? this.primaryDrawing : this.secondaryDrawing;
        var syncDrawing = (source == 'primary') ? this.secondaryDrawing : this.primaryDrawing;
        var doodleToSync = msgArray['object'].doodle;

        for (var doodleClass in syncArray['other']) {
            if (doodleClass === doodleToSync.className) {
                this.doDoodleSync(drawing, syncDrawing, msgArray, syncArray['other'][doodleClass]);
            }
        }
    };

    AnteriorSegmentController.prototype.doDoodleSync = function(drawing, syncDrawing, msgArray, syncDoodles)
    {
        var doodleToSync = msgArray['object'].doodle;
        for (var syncClass in syncDoodles) {
            if (syncDrawing.hasDoodleOfClass(syncClass)) {
                var syncDoodle = syncDrawing.firstDoodleOfClass(syncClass);
                if (syncDoodle.willSync) {
                    var parameterArray = syncDoodles[syncClass]['parameters'];
                    for (var i = 0; i < parameterArray.length; i++) {
                        if (parameterArray[i] == msgArray.object.parameter) {
                            if (doodleToSync[parameterArray[i]] != syncDoodle[parameterArray[i]]) {
                                if (typeof(msgArray.object.value) == 'string') {
                                    syncDoodle.setParameterFromString(msgArray.object.parameter, msgArray.object.value, true);
                                }
                                else {
                                    var increment = msgArray.object.value - msgArray.object.oldValue;
                                    var newValue = syncDoodle[msgArray.object.parameter] + increment;

                                    // Sync slave parameter to value of master
                                    syncDoodle.setSimpleParameter(msgArray.object.parameter, newValue);
                                    syncDoodle.updateDependentParameters(msgArray.object.parameter);

                                    // Update any bindings associated with the slave doodle
                                    syncDrawing.updateBindings(syncDoodle);
                                }
                            }
                        }
                    }
                }
            }
        }
        syncDrawing.repaint();
    };

    return AnteriorSegmentController;
})(ED);

function anteriorSegmentListener(_drawing)
{
    if (!$(_drawing.canvas).data('controller')) {
        $(_drawing.canvas).data('controller', new OpenEyes.OphCiExamination.AnteriorSegmentController(_drawing));
    }
}
