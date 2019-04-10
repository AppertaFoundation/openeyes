var OpenEyes = OpenEyes || {};

OpenEyes.OphCiExamination = OpenEyes.OphCiExamination || {};

OpenEyes.OphCiExamination.AnteriorSegmentController = (function (ED) {
  /**
   *
   * @param options
   * @constructor
   */
  function AnteriorSegmentController(options) {
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
      Lens: 'LensCrossSection',
      AntSeg: 'AntSegCrossSection',
      Cornea: 'CorneaCrossSection',
      PCIOL: 'PCIOLCrossSection',
      ToricPCIOL: 'ToricPCIOLCrossSection',
      ACIOL: 'ACIOLCrossSection',
      CornealOpacity: 'CornealOpacityCrossSection',
      Hypopyon: 'HypopyonCrossSection',
      CornealGraft: 'CornealGraftCrossSection',
      Hyphaema: 'HyphaemaCrossSection',
      EndothelialKeratoplasty: 'EndothelialKeratoplastyCrossSection',
      CornealThinning: 'CornealThinningCrossSection',
    }
  };

  /**
   * Assign the primary drawing canvas to the controller
   *
   * @param drawing
   */
  AnteriorSegmentController.prototype.setPrimary = function (drawing) {
    if (!this.primaryDrawing) {
      this.primaryDrawing = drawing;
      this.primaryDrawing.registerForNotifications(this, 'primaryDrawingNotification', [
          'afterReset',
          'ready',
          'doodlesLoaded',
          'beforeReset',
          'doodleSelected',
          'doodleAdded',
          'doodleDeleted',
          'parameterChanged',
      ]);
    }
  };

  /**
   * Assign the secondary (cross section) canvas to the controller
   *
   * @param drawing
   */
  AnteriorSegmentController.prototype.setSecondary = function (drawing) {
    this.secondaryDrawing = drawing;
    this.secondaryDrawing.registerForNotifications(this, 'secondaryDrawingNotification', ['ready', 'parameterChanged', 'doodlesLoaded', 'doodleSelected']);
  };

  /**
   * Assign the gonioscopy canvas to the controller
   *
   * @param drawing
   */
  AnteriorSegmentController.prototype.setGonioscopyDrawing = function (drawing) {
    this.gonioscopyDrawing = drawing;
    this.gonioscopyDrawing.registerForNotifications(this, 'gonioscopyNotification', [
        'afterReset',
        'ready',
        'parameterChanged',
    ]);
  };

  /**
   * Set up any internal references needed by the controller
   */
  AnteriorSegmentController.prototype.initialise = function () {
    // Cache references to the form elements that will be affected by changes to the Eyedraws
    this.$edReportField = $('#OEModule_OphCiExamination_models_Element_OphCiExamination_AnteriorSegment_' + this.options.side + '_ed_report');
    this.$edReportDisplay = $('#OEModule_OphCiExamination_models_Element_OphCiExamination_AnteriorSegment_' + this.options.side + '_ed_report_display');
    this.$nuclearCataract = $('#OEModule_OphCiExamination_models_Element_OphCiExamination_AnteriorSegment_' + this.options.side + '_nuclear_id');
    this.$corticalCataract = $('#OEModule_OphCiExamination_models_Element_OphCiExamination_AnteriorSegment_' + this.options.side + '_cortical_id');
    this.secondaryDoodlesLoaded = false;
    this.resetting = false;
    // reverse lookup for syncing secondary doodles back to the primary canvas
    this.options.reversePairArray = {};
    for (var primaryClassName in this.options.pairArray) {
      if (this.options.pairArray.hasOwnProperty(primaryClassName)) {
        this.options.reversePairArray[this.options.pairArray[primaryClassName]] = primaryClassName;
      }
    }
  };

  /**
   * Check if the primary canvas is ready
   *
   * @returns boolean
   */
  AnteriorSegmentController.prototype.primaryDrawingReady = function () {
    return this.primaryDrawing && this.primaryDrawing.isReady;
  };

  /**
   * Check if the secondary canvas is ready
   *
   * @returns boolean
   */
  AnteriorSegmentController.prototype.secondaryDrawingReady = function () {
    return this.secondaryDrawing && this.secondaryDrawing.isReady;
  };

  /**
   * Update the display div of the eyedraw report
   */
  AnteriorSegmentController.prototype.updateReport = function () {
    this.$edReportDisplay.html(this.$edReportField.val().replace(/\n/g, '<br />'));
  };

  /**
   * Store the given value to the provided input element (syncing of data items that need to
   * be recorded against the element (and therefore in the backend db) for reporting/analysis)
   *
   * @param $el
   * @param lookupValue
   */
  AnteriorSegmentController.prototype.storeToHiddenField = function ($el, lookupValue) {
    var map = $el.data('eyedraw-map');
    if (lookupValue in map) {
      $el.val(map[lookupValue]);
    } else {
      $el.val($el.data('eyedraw-default'));
    }
    $el.trigger('change');
  };

  /**
   * Abstraction of setting a parameter value from one doodle to another.
   *
   * @param source
   * @param sourceParameter
   * @param destination
   * @param destinationParameter
   * @param repaint
   */
  AnteriorSegmentController.prototype.setDoodleParameter = function (source, sourceParameter, destination, destinationParameter, repaint) {
    // no need to trigger parameter changes if they are already matched.
    if (source[sourceParameter] == destination[destinationParameter]) {
      return;
    }


    if (typeof(source[sourceParameter]) === "string") {
      destination.setParameterFromString(destinationParameter, source[sourceParameter], true);
    } else {
      destination.setSimpleParameter(destinationParameter, source[sourceParameter]);
    }
    destination.updateDependentParameters(destinationParameter);
    if (repaint)
      destination.drawing.repaint();
  };

  /**
   * Drives the loading of the cross section doodles from the doodles in the primary (enface) view
   */
  AnteriorSegmentController.prototype.loadSecondaryDoodles = function () {
    if (!this.secondaryDoodlesLoaded) {
      for (var i = 0; i < this.primaryDrawing.doodleArray.length; i++) {
        var doodle = this.primaryDrawing.doodleArray[i];
        if (this.options.pairArray.hasOwnProperty(doodle.className)) {
          // it's a doodle that we want to pair into the secondary Drawing
          var secondaryClass = this.options.pairArray[doodle.className];
          // create the doodle
          var secondaryDoodle = this.secondaryDrawing.addDoodle(secondaryClass);
          // then ensure we've got all the parameters set correctly.
          var syncParameters = secondaryDoodle.getLinkedParameters(doodle.className);
          if (typeof(syncParameters) !== "undefined") {
            for (var j in syncParameters['source']) {
              var parameter = syncParameters['source'][j];
              this.setDoodleParameter(doodle, parameter, secondaryDoodle, parameter);
            }
            for (var j in syncParameters['store']) {
              var pMap = syncParameters['store'][j];
              this.setDoodleParameter(doodle, pMap[1], secondaryDoodle, pMap[0]);
            }
          }
        }
      }
      this.secondaryDrawing.resetDoodleSet = window.JSON.parse('[' + this.secondaryDrawing.json() + ']');
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
  AnteriorSegmentController.prototype.updatePrimaryParameter = function (edClass, parameterName, changedParameter) {
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
   * Synchronise iris colour from Gonioscopy if Gonioscopy section exists
   *
   */
  AnteriorSegmentController.prototype.SyncIrisColourFromGonioscopy = function() {
    if(this.gonioscopyDrawing && $(".OEModule_OphCiExamination_models_Element_OphCiExamination_Gonioscopy")[0]) {
      let angleGradeNorthDoodle = this.gonioscopyDrawing.firstDoodleOfClass('AngleGradeNorth');
      let anteriorSegmentDoodle = this.primaryDrawing.firstDoodleOfClass('AntSeg');
      if(angleGradeNorthDoodle && anteriorSegmentDoodle) {
        this.setDoodleParameter(angleGradeNorthDoodle, 'colour', anteriorSegmentDoodle, 'colour', true);
      }
    }
  };

  /**
   * Handler of notifications from the primary Eyedraw canvas
   * All changes require the report to be updated, but additional behaviours also arise
   * depending on the notification type.
   *
   * @param msgArray
   */
  AnteriorSegmentController.prototype.primaryDrawingNotification = function (msgArray) {
    switch (msgArray['eventName']) {
      case 'afterReset':
        this.SyncIrisColourFromGonioscopy();
        break;
      case 'ready':
        if (this.secondaryDrawingReady()) {
          this.loadSecondaryDoodles();
        }
        //when PCR risk was already open and AnteriorSegment opened we need to syn with the PCR element
        //pcr_init can be defined multiple places depening on the event type e.g.: form_Element_OphCiExamination_PcrRisk.php
        if(typeof pcr_init === 'function'){
            pcr_init();
        }
        this.SyncIrisColourFromGonioscopy();
        break;
      case 'doodlesLoaded':
        if (this.resetting) {
          this.secondaryDrawing.resetEyedraw();
          this.resetting = false;
        }
        break;

      case 'doodleSelected':
        // Ensure that selecting a doodle in one drawing de-deselects the others
        if (this.secondaryDrawingReady()) {
          this.secondaryDrawing.deselectDoodles();
        }
        break;

      case 'beforeReset':
        this.resetting = true;
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
        if (this.resetting) {
          // don't worry about delete notifications whilst resetting eyedraws.
          break;
        }
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
        if (this.secondaryDoodlesLoaded) {
          if (this.options.pairArray[change.doodle.className] !== undefined) {
            // get the corresponding secondary doodle and it's sync parameter definitions
            var secondaryDoodle = this.secondaryDrawing.firstDoodleOfClass(this.options.pairArray[change.doodle.className]);
            if (secondaryDoodle) {
              // if we're resetting or anything along those lines, the secondaryDoodle might not be present.
              var syncParameters = secondaryDoodle.getLinkedParameters(change.doodle.className);
              if (typeof(syncParameters) !== "undefined") {
                // loop through source synced params and update if matches this primary parameter
                for (var j in syncParameters['source']) {
                  if (syncParameters['source'][j] === change.parameter) {
                    this.setDoodleParameter(change.doodle, change.parameter, secondaryDoodle, change.parameter, true);
                    break;
                  }
                }
                // we know that that we don't have to sync 'store' params to the secondary so we're done here
              }
            }
          }
        }


        if (change.doodle.className === 'Lens') {
          if (change.parameter === 'nuclearGrade') {
            this.storeToHiddenField(this.$nuclearCataract, change.value);
          }
          if (change.parameter === 'corticalGrade') {
            this.storeToHiddenField(this.$corticalCataract, change.value);
          }
        }

        if (change.doodle.className === 'AntSeg' && change.parameter === 'colour' && this.gonioscopyDrawing) {
          if($(".OEModule_OphCiExamination_models_Element_OphCiExamination_Gonioscopy")[0]) { // whether Goinoscopy section exists
            let angleGradeNorthDoodle = this.gonioscopyDrawing.firstDoodleOfClass("AngleGradeNorth");
            let anteriorSegmentDoodle = change.doodle;
            this.setDoodleParameter(anteriorSegmentDoodle, 'colour', angleGradeNorthDoodle, 'colour', true);
          }
        }
        break;
    }
  };


  /**
   * Handler of notifications from the Gonioscopy canvas for synchronise iris colour
   *
   * @param msgArray
   */
  AnteriorSegmentController.prototype.gonioscopyNotification = function (msgArray) {
    let anteriorSegmentDoodle = this.primaryDrawing.firstDoodleOfClass('AntSeg');
    if ($(".OEModule_OphCiExamination_models_Element_OphCiExamination_AnteriorSegment")[0] && anteriorSegmentDoodle) {
      // if Anterior Segment section does not exist, then sync is not needed
      let change;
      let angleGradeNorthDoodle;
      switch (msgArray['eventName']) {
        case 'afterReset':
        case 'ready':
          angleGradeNorthDoodle = this.gonioscopyDrawing.firstDoodleOfClass("AngleGradeNorth");
          if (angleGradeNorthDoodle && anteriorSegmentDoodle.colour) {
            this.setDoodleParameter(anteriorSegmentDoodle, 'colour', angleGradeNorthDoodle, 'colour', true);
          }
          break;
        case 'parameterChanged':
          change = msgArray['object'];
          if (change.doodle.className === 'AngleGradeNorth' && change.parameter === 'colour') {
            let angleGradeNorthDoodle = change.doodle;
            this.setDoodleParameter(angleGradeNorthDoodle, 'colour', anteriorSegmentDoodle, 'colour', true);
          }
          break;
      }
    }
  };

  /**
   * Handle secondary drawing notifications
   *
   * @param msgArray
   */
  AnteriorSegmentController.prototype.secondaryDrawingNotification = function (msgArray) {
    if (this.resetting) {
      return;
    }

    switch (msgArray['eventName']) {
      case 'ready':
        if (this.primaryDrawingReady())
          this.loadSecondaryDoodles();

          // in order to get the PCR Risk pupil size to update from the side view...
          // launch the anterior segment popup in order to load the relevant pupilSize_control input into the DOM
          $('.ed-doodle-popup').css('visibility','hidden'); // not needed for functionality but it stops the popup flashing onto the screen before it is removed 
          
          setTimeout(function(){
            $('.ed-selected-doodle-select').val('Anterior segment').trigger('change');
          },0);

          // reset the select input to its initial state
          setTimeout(function(){
            $('.ed-doodle-popup').addClass('closed').css('visibility',''); // to stop it from flashing
            $('.ed-selected-doodle-select').val('None').trigger('change');
          },500);
        break;
      case 'doodlesLoaded':
        break;
      case 'doodleSelected':
        if (this.primaryDrawingReady())
          this.primaryDrawing.deselectDoodles();
        break;
      case 'parameterChanged':
        if (this.secondaryDoodlesLoaded) {
          // work out what parameter and on what doodle should an update be carried out on
          // the primary canvas.
          var change = msgArray['object'];
          if (this.options.reversePairArray[change.doodle.className] !== undefined) {
            var primaryDoodle = this.primaryDrawing.firstDoodleOfClass(this.options.reversePairArray[change.doodle.className]);
            var syncParameters = change.doodle.getLinkedParameters(this.options.reversePairArray[change.doodle.className]);
            if (typeof(syncParameters) !== "undefined") {
              var synced = false;
              for (var j in syncParameters['source']) {
                if (syncParameters['source'][j] === change.parameter) {
                  this.setDoodleParameter(change.doodle, change.parameter, primaryDoodle, change.parameter, true);
                  synced = true;
                  break;
                }
              }
              if (!synced) {
                for (var j in syncParameters['store']) {
                  if (syncParameters['store'][j][0] === change.parameter) {
                    this.setDoodleParameter(change.doodle, change.parameter, primaryDoodle, syncParameters['store'][j][1], true);
                    break;
                  }
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

var anteriorSegmentDrawings = [];

function anteriorSegmentListener(_drawing) {
  var canvas = $(_drawing.canvas);
  anteriorSegmentDrawings[_drawing.eye] = _drawing;
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
    if(typeof gonioscopyDrawings !== 'undefined') {
      let gonioscopyDrawing = gonioscopyDrawings[_drawing.eye];
      if(gonioscopyDrawing) {
        controller.setGonioscopyDrawing(gonioscopyDrawing);
      }
    }
  }
}
