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
      CellsAndFlare: 'CellsAndFlareCrossSection'
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
    this.secondaryDrawing.registerForNotifications(this, 'secondaryDrawingNotification', ['ready', 'parameterChanged', 'doodlesLoaded', 'doodleSelected', 'doodleAdded']);
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
    for (let primaryClassName in this.options.pairArray) {
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
    let map = $el.data('eyedraw-map');
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
    let getSecondaryDoodles = (doodle, secondaryClass) => {
      return [this.secondaryDrawing.addDoodle(secondaryClass)];
    };
    this.bindSecondaryDoodles(getSecondaryDoodles);
    this.secondaryDrawing.resetDoodleSet = window.JSON.parse('[' + this.secondaryDrawing.json() + ']');
    this.secondaryDoodlesLoaded = true;
  };

  /**
   * Link doodles after reset
   */
  AnteriorSegmentController.prototype.refreshSecondaryDoodles = function () {
    let getSecondaryDoodles = (doodle, secondaryClass) => {
      return this.secondaryDrawing.doodleArray.filter(
        doodle => doodle.className === secondaryClass
      );
    };
    this.bindSecondaryDoodles(getSecondaryDoodles);
  };

  /**
   * Makes sure primary and secondary doodles are linked.
   */
  AnteriorSegmentController.prototype.bindSecondaryDoodles = function (getSecondaryDoodles) {
    for (let i = 0; i < this.primaryDrawing.doodleArray.length; i++) {
      let doodle = this.primaryDrawing.doodleArray[i];
      if (this.options.pairArray.hasOwnProperty(doodle.className)) {
        // it's a doodle that we want to pair into the secondary Drawing
        let secondaryClass = this.options.pairArray[doodle.className];
        // fetch matching doodles
        let secondaryDoodles = getSecondaryDoodles(doodle, secondaryClass);

        // then ensure we've got all the parameters set correctly.
        secondaryDoodles.forEach( secondaryDoodle => {
          let syncParameters = secondaryDoodle.getLinkedParameters(doodle.className);
          if (typeof(syncParameters) !== "undefined") {
            for (let j in syncParameters['source']) {
              let parameter = syncParameters['source'][j];
              this.setDoodleParameter(doodle, parameter, secondaryDoodle, parameter);
            }
            for (let j in syncParameters['store']) {
              let pMap = syncParameters['store'][j];
              this.setDoodleParameter(doodle, pMap[1], secondaryDoodle, pMap[0]);
            }
            this.setDoodleParameter(doodle, 'id', secondaryDoodle, 'linkedDoodle');
          }
        });
      }
    }
    this.secondaryDrawing.deselectDoodles();
  };

  /**
   * Syncing parameters back to the enface doodles that are not "naturally" synced
   *
   * @param edClass - the class of doodle on the primary drawing
   * @param parameterName - the parameter name on the primary doodle that should be updated
   * @param changedParameter - the changedParameter object from Eyedraw
   */
  AnteriorSegmentController.prototype.updatePrimaryParameter = function (edClass, parameterName, changedParameter) {
    let primaryDoodle = this.primaryDrawing.firstDoodleOfClass(edClass);
    // avoid infinite loop of continually updating the parameter through notifications
    if (primaryDoodle[parameterName] == changedParameter.value)
      return;

    if (typeof(changedParameter.value) === "string") {
      primaryDoodle.setParameterFromString(parameterName, changedParameter.value, true);
    } else {
      let increment = changedParameter.value - changedParameter.oldValue;
      let newValue = primaryDoodle[parameterName] + increment;

      // Sync slave parameter to value of master
      primaryDoodle.setSimpleParameter(parameterName, newValue);
      primaryDoodle.updateDependentParameters(parameterName);
    }
  };

  /**
   * Synchronise iris colour from Gonioscopy if Gonioscopy section exists
   *
   */
  AnteriorSegmentController.prototype.SyncIrisColourWithGonioscopy = function() {
    if(this.gonioscopyDrawing && $(".OEModule_OphCiExamination_models_Element_OphCiExamination_Gonioscopy")[0]) {
      let angleGradeNorthDoodle = this.gonioscopyDrawing.firstDoodleOfClass('AngleGradeNorth');
      let anteriorSegmentDoodle = this.primaryDrawing.firstDoodleOfClass('AntSeg');
      if(angleGradeNorthDoodle && anteriorSegmentDoodle) {
        const defaultIrisColour = (typeof default_iris_colour) !== 'undefined' ? default_iris_colour : 'Blue';
        // if angleGradeNorthDoodle.colour is default and anteriorSegmentDoodle.colour is not,
        // we can assume that anteriorSegmentDoodle has been changed and the other hasn't
        const anteriorSegmentDoodleIsTheSource = angleGradeNorthDoodle.colour === defaultIrisColour && anteriorSegmentDoodle.colour !== defaultIrisColour;
        const sourceDoodle      = anteriorSegmentDoodleIsTheSource ? anteriorSegmentDoodle : angleGradeNorthDoodle;
        const destinationDoodle = anteriorSegmentDoodleIsTheSource ? angleGradeNorthDoodle : anteriorSegmentDoodle;
        this.setDoodleParameter(sourceDoodle, 'colour', destinationDoodle, 'colour', true);
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
        this.refreshSecondaryDoodles();
        this.SyncIrisColourWithGonioscopy();
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
        this.SyncIrisColourWithGonioscopy();
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

      case 'doodleAdded': {
        // Newly added doodle is passed in message object
        let newDoodle = msgArray['object'];

        // Remove any doodles that violate unique group property with added doodle
        if ('classGroupUnique' in newDoodle) {
          let offendingDoodles = newDoodle.drawing.doodleArray.filter(
            doodle => (doodle.className !== newDoodle.className) &&
                      ('classGroupUnique' in doodle) &&
                      (doodle.classGroupUnique === newDoodle.classGroupUnique)
          );
          offendingDoodles.forEach( doodle => {
            newDoodle.drawing.deleteDoodlesOfClass(doodle.className);
          });
        }

        // Check pair array for doodle to add to secondary
        if (this.secondaryDrawingReady() && this.secondaryDoodlesLoaded) {
          for (let primaryClass in this.options.pairArray) {
            if (newDoodle.className == primaryClass) {
              let secondaryClass = this.options.pairArray[primaryClass];
              if (!this.secondaryDrawing.hasDoodleOfClass(secondaryClass) || !newDoodle.isUnique) {
                const secondaryDoodle = this.secondaryDrawing.addDoodle(secondaryClass);
                this.setDoodleParameter(newDoodle, 'id', secondaryDoodle, 'linkedDoodle');


                // Adjust position in relation to other doodles (array defined in the doodle subclass)
                for (let i = 0; i < secondaryDoodle.inFrontOfClassArray.length; i++) {
                  secondaryDoodle.drawing.moveNextTo(secondaryDoodle, secondaryDoodle.inFrontOfClassArray[i], true);
                }

                for (let i = 0; i < secondaryDoodle.behindClassArray.length; i++) {
                  secondaryDoodle.drawing.moveNextTo(secondaryDoodle, secondaryDoodle.behindClassArray[i], false);
                }

                this.secondaryDrawing.deselectDoodles();
                this.primaryDrawing.selectDoodle(newDoodle);
              }
            }
          }
        }

        if (newDoodle.className === 'Lens') {
          // ensure the initial value is stored correctly.
          this.storeToHiddenField(this.$nuclearCataract, newDoodle.nuclearGrade);
          this.storeToHiddenField(this.$corticalCataract, newDoodle.corticalGrade);
        }

        // Adjust position in relation to other doodles (array defined in the doodle subclass)
        for (let i = 0; i < newDoodle.inFrontOfClassArray.length; i++) {
          newDoodle.drawing.moveNextTo(newDoodle, newDoodle.inFrontOfClassArray[i], true);
        }

        for (let i = 0; i < newDoodle.behindClassArray.length; i++) {
          newDoodle.drawing.moveNextTo(newDoodle, newDoodle.behindClassArray[i], false);
        }
        break;
      }
      case 'doodleDeleted': {
        if (this.resetting) {
          // don't worry about delete notifications whilst resetting eyedraws.
          break;
        }
        // Class of deleted doodle
        let deletedDoodle = msgArray['object'];
        let deletedDoodleClass = deletedDoodle.className;

        // check if a pair doodle should be removed from secondary
        if (this.secondaryDrawingReady()) {
          for (let primaryClass in this.options.pairArray) {
            if (deletedDoodleClass == primaryClass) {

              let secondaryClass = this.options.pairArray[primaryClass];
              if (this.secondaryDrawing.hasDoodleOfClass(secondaryClass)) {
                this.secondaryDrawing.deleteDoodleOfId(this.getLinkedSecondaryDoddle(deletedDoodle).id);
                this.secondaryDrawing.deselectDoodles();
              }
            }
          }
        }

        if (deletedDoodleClass === 'CornealGraft') {
          let individualSutures = this.primaryDrawing.allDoodlesOfClass("CornealSuture");
          let continuousSutures = this.primaryDrawing.allDoodlesOfClass("ContinuousCornealSuture");
          let sutures = individualSutures.concat(continuousSutures);
          for (let i = 0; i < sutures.length; i++) {
                let suture = sutures[i];
                if (suture.cornealGraft && !this.primaryDrawing.doodleOfId(suture.cornealGraft.id)) {
                  suture.cornealGraft = null;
                  suture.setParameterFromString('originX', '0', true);
                  suture.setParameterFromString('originY', '0', true);
                }
            }
        }

        if (deletedDoodleClass === 'Lens') {
          // reset to the null value as this removes any cataract value
          this.storeToHiddenField(this.$nuclearCataract, '');
          this.storeToHiddenField(this.$corticalCataract, '');
        }
        break;
      }
      case 'parameterChanged': {
        let change = msgArray['object'];
        let selectedDoodle = msgArray['selectedDoodle'];
        if (this.secondaryDoodlesLoaded) {
          if (this.options.pairArray[change.doodle.className] !== undefined) {
            // get the corresponding secondary doodle and it's sync parameter definitions
            const secondaryDoodle = this.getLinkedSecondaryDoddle(change.doodle);
            if (secondaryDoodle && selectedDoodle && change.doodle.id === selectedDoodle.id) {
              // if we're resetting or anything along those lines, the secondaryDoodle might not be present.
              let syncParameters = secondaryDoodle.getLinkedParameters(change.doodle.className);
              if (typeof(syncParameters) !== "undefined") {
                // loop through source synced params and update if matches this primary parameter
                for (let j in syncParameters['source']) {
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
      switch (msgArray['eventName']) {
        case 'afterReset': {
            let angleGradeNorthDoodle = this.gonioscopyDrawing.firstDoodleOfClass("AngleGradeNorth");
            if (angleGradeNorthDoodle && anteriorSegmentDoodle.colour) {
              this.setDoodleParameter(anteriorSegmentDoodle, 'colour', angleGradeNorthDoodle, 'colour', true);
            }
          }
          break;
        case 'ready':
          this.SyncIrisColourWithGonioscopy();
          break;
        case 'parameterChanged': {
          let change = msgArray['object'];
          if (change.doodle.className === 'AngleGradeNorth' && change.parameter === 'colour') {
            let angleGradeNorthDoodle = change.doodle;
            this.setDoodleParameter(angleGradeNorthDoodle, 'colour', anteriorSegmentDoodle, 'colour', true);
          }
          break;
        }
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

        if (msgArray.selectedDoodle.className === 'CellsAndFlareCrossSection') {
          let cellsAndFlareDoodle = this.primaryDrawing.firstDoodleOfClass('CellsAndFlare');
          // ugly, but the mousedown event on the side canvas needs to finish, before deselecting it
          setTimeout(() => this.primaryDrawing.setDoodleAsSelected(cellsAndFlareDoodle.id), 100);
        }
        break;
      case 'parameterChanged':
        if (this.secondaryDoodlesLoaded) {
          // work out what parameter and on what doodle should an update be carried out on
          // the primary canvas.
          const change = msgArray['object'];
          if (this.options.reversePairArray[change.doodle.className] !== undefined) {
            const primaryDoodle = this.primaryDrawing.doodleOfId(change.doodle.linkedDoodle);
            if (!primaryDoodle) {
              break;
            }
            const syncParameters = change.doodle.getLinkedParameters(this.options.reversePairArray[change.doodle.className]);
            if (typeof(syncParameters) !== "undefined") {
              let synced = false;
              for (let j in syncParameters['source']) {
                if (syncParameters['source'][j] === change.parameter) {
                  this.setDoodleParameter(change.doodle, change.parameter, primaryDoodle, change.parameter, true);
                  synced = true;
                  break;
                }
              }
              if (!synced) {
                for (let j in syncParameters['store']) {
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
      case 'doodleAdded':
        let newDoodle = msgArray['object'];
        // Adjust position in relation to other doodles (array defined in the doodle subclass)
        for (let i = 0; i < newDoodle.inFrontOfClassArray.length; i++) {
          newDoodle.drawing.moveNextTo(newDoodle, newDoodle.inFrontOfClassArray[i], true);
        }

        for (let i = 0; i < newDoodle.behindClassArray.length; i++) {
          newDoodle.drawing.moveNextTo(newDoodle, newDoodle.behindClassArray[i], false);
        }
        break;
    }
  };

  AnteriorSegmentController.prototype.getLinkedSecondaryDoddle = function(doodle){
    const doodlesOfClass = this.secondaryDrawing.allDoodlesOfClass(this.options.pairArray[doodle.className]);

    if(doodlesOfClass.length){
      for(let i = doodlesOfClass.length - 1; i >= 0; i--) {
        if(doodlesOfClass[i].linkedDoodle === doodle.id){
          return doodlesOfClass[i];
        }
      }
    }
    return false;
  };

  return AnteriorSegmentController;
})(ED);

function anteriorSegmentListener(_drawing) {
  let canvas = $(_drawing.canvas);
  let drawingId = $(_drawing.canvas).attr('id');
  let secondary = drawingId.endsWith('_side');
  if (secondary) {
    canvas = $('#' + drawingId.substring(0, drawingId.length - 5));
  }
  let controller = canvas.data('controller');
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

    const gonioscopyCanvas = $(".OEModule_OphCiExamination_models_Element_OphCiExamination_Gonioscopy").
      find("[data-side='" + (_drawing.eye === 1 ? "left" : "right") + "']").
      find('canvas');
    const gonioscopyDrawing = ED.getInstance(gonioscopyCanvas.data('drawing-name'));
    if(gonioscopyDrawing) {
      controller.setGonioscopyDrawing(gonioscopyDrawing);
    }
  }
}
