/**
 * Controller for managing the feature tours on the front end.
 *
 * NB. development at this point has focused on the tours, and
 * not the splashScreen or downloadLinks options.
 *
 * @param splashScreen
 * @param tours
 * @param downloadLinks
 * @constructor
 */
function NewFeatureHelpController(splashScreen, tours, downloadLinks, options) {

  this.options = $.extend(true, {}, NewFeatureHelpController._defaultOptions, options);

  this.tourDefinitions = this.options.tourDefinitions;
  this.autoTours = this.options.autoTours;
  this.splashScreenElements = this.options.splashScreenElements;
  this.sleepPeriod = this.options.sleepPeriod;

    /**
     * The purpose of this flag is that the auto start of the tour can be turned off from admin screen
     * autoStart false won't let start the tour even if 'auto' flag is true in the protected/tour/config
     * @type {*|boolean}
     */
  this.autoStart = this.options.autoStart;

  this.downloadLinks = downloadLinks ? downloadLinks : [];

  this._initTours(tours);
  this._initSplashScreen(splashScreen);
  if (Object.keys(this.tourDefinitions).length
      || this.downloadLinks.length
      || this.splashScreenElements.length) {
    // we have some help to show
    this._addListeners();

    if(this.autoStart === true){
        this._autoStart();
    }

  } else {
    $('.help-trigger-btn').hide();
  }

}

NewFeatureHelpController._defaultOptions = {
    tourDefinitions: {},
    autoTours: [],
    splashScreenElements: [],
    sleepPeriod: undefined,
    autoStart: true
};

NewFeatureHelpController.prototype._addListeners = function() {
  $('.help-trigger-btn').on('click',this.togglePopup.bind(this));
  $('#help-splash-screen-btn').on('click',this.toggleSplashScreen.bind(this));
  $('.help-action-tour').on('click',this._toggleTour.bind(this));
  $('.help-close').on('click',this.togglePopup.bind(this));
};

NewFeatureHelpController.prototype._autoStart = function() {
  if (this.autoTours.length) {
    this.startTour(this.autoTours.pop());
  } else {
    // no more auto tours, so ensure sleep period is reset
    this.sleepPeriod = undefined;
  }
};

NewFeatureHelpController.prototype._toggleTour = function(evt) {
  let $button = $(evt.currentTarget);
  let buttonTourId = $button.prop('id').slice(15);
  this.toggleTour(buttonTourId);
}

NewFeatureHelpController.prototype.toggleTour = function(tourId) {
  if (this.tourDefinitions[tourId]['_bsTour'].ended()) {
    this.startTour(tourId);
  } else {
    this.endAllTours();
  }
}

NewFeatureHelpController.prototype.togglePopup = function() {
    let $popup = $('.help-popup');
    if ($popup.css('display') !== 'none') {
        $popup.hide();
        this.endAllTours();
        this.hideSplashScreen();
    } else {
        $popup.show();
        this.showSplashScreen();
    }
}

NewFeatureHelpController.prototype.toggleSplashScreen = function() {
  if ($('#help-body-overlay').css('display') !== 'none') {
    this.hideSplashScreen();
  } else {
    this.showSplashScreen();
  }
}

NewFeatureHelpController.prototype.showSplashScreen = function() {
  let $button = $('#help-splash-screen-btn');
  $button.html('Hide Splash Screen');
  $button.removeClass('help-action');
  $button.addClass('help-action-active');
  this.endAllTours();
  window.scrollTo(0,0);
  this._showSplashScreen();
}

NewFeatureHelpController.prototype.hideSplashScreen = function() {
  let $button = $('#help-splash-screen-btn');
  $button.html('Show Splash Screen');
  $button.removeClass('help-action-active');
  $button.addClass('help-action');
  this.endAllTours();
  this._hideSplashScreen();
}

NewFeatureHelpController.prototype._showSplashScreen = function() {
  this.splashScreenElements.forEach(($element) => {
    if ($element.data('showparent')) {
      $element.parent().css('z-index','161');
    }
    $element.css('z-index','161');
  });
  $('#help-body-overlay').show();
  $('#help-header-overlay').show();
  $('[data-toggle="popover"]').popover('show');
}

NewFeatureHelpController.prototype._hideSplashScreen = function() {
  this.splashScreenElements.forEach(($element) => {
    if ($element.data('showparent')) {
      $element.parent().css('z-index','');
    }
    $element.css('z-index','');
  });
  $('#help-body-overlay').hide();
  $('#help-header-overlay').hide();
  $('[data-toggle="popover"]').popover('hide');
}

NewFeatureHelpController.prototype.startTour = function(tourId) {
  this.hideSplashScreen();
  this.endAllTours();
  this.tourDefinitions[tourId]['_bsTour'].restart();
}

NewFeatureHelpController.prototype.endAllTours = function() {
  for (var tourId in this.tourDefinitions) {
    if (this.tourDefinitions.hasOwnProperty(tourId)) {
      let tourObj = this.tourDefinitions[tourId]['_bsTour'];
      if (!tourObj.ended()) {
        tourObj.end();
      }
    }
  }
}

NewFeatureHelpController.prototype._initTours = function(tours) {
  for (var idx in tours) {
    if (!tours.hasOwnProperty(idx)) {
      continue;
    }

    let definition = tours[idx];
    let tourId = definition['id'];
    if (definition['auto']) {
        this.autoTours.push(tourId);
    }
    this.tourDefinitions[tourId] = definition;
    this.tourDefinitions[tourId]['_bsTour'] = new Tour(
      {
        name: tourId,
        backdrop: true,
        keyboard: true,
        storage: window.localStorage,
        steps: definition['steps'],
        template: this._tourTemplate.bind(this, tourId),
        onEnd: this._tourEnded.bind(this, tourId),
        onStart: this._tourStarted.bind(this, tourId),
        onShown: this._tourShown.bind(this, tourId),
        onHide: this._tourHide.bind(this, tourId)
      }
    );
  }
}

NewFeatureHelpController.prototype._initSplashScreen = function(splashScreen) {
  splashScreen.forEach((popup) => {
    var $element = $(popup.element);
    $element.attr('title', popup.title);
    $element.attr('data-toggle','popover');
    for (var data in popup) {
      if (!['element','title'].includes(data)) {
        $element.attr(`data-${data}`,popup[data]);
      }
    }
    this.splashScreenElements.push($element);
  });
}

NewFeatureHelpController.prototype._tourEnded = function(tourId) {
  $('.popover').remove();
  let $button = $(`#help-tour-name-${tourId}`);
  let definition = this.tourDefinitions[tourId];
  this._checkEndedState(tourId);
  $button.html(`Start ${definition['name']}`);
  $button.removeClass('help-action-active');
  $button.addClass('help-action');
  this._autoStart();
}

NewFeatureHelpController.prototype._tourStarted = function(tourId) {
  var definition = this.tourDefinitions[tourId];
  definition['_bsTour'].goTo(0);

  let $button = $(`#help-tour-name-${tourId}`);
  $button.html(`End ${definition['name']}`);
  $button.removeClass('help-action');
  $button.addClass('help-action-active');
}

/**
 * When a tour is ended, check if the user saw the last slide, and mark
 * it as complete for them in the backend.
 *
 * @param tourId
 */
NewFeatureHelpController.prototype._checkEndedState = function(tourId) {
  let definition = this.tourDefinitions[tourId];
  let tour = definition['_bsTour'];

  if (tour.getStep(tour.getCurrentStep()+1)) {
    // not at the end of the tour, which indicates early dismissal
    if (this.sleepPeriod) {
      $.post(
        '/FeatureTour/sleep?id=' + tourId,
        {
          YII_CSRF_TOKEN: YII_CSRF_TOKEN,
          period: this.sleepPeriod
        }
      );
    }
  } else {
    $.post(
      '/FeatureTour/complete?id=' + tourId,
      {YII_CSRF_TOKEN: YII_CSRF_TOKEN}
    );
  }
}

NewFeatureHelpController.prototype._tourHide = function(tourId) {
  // store the sleep value if the element is there
  if ($('select[name="sleep-period"]').length)
    this.sleepPeriod = $('select[name="sleep-period"]').val();
}

NewFeatureHelpController.prototype._tourShown = function(tourId) {
  let definition = this.tourDefinitions[tourId];
  let tour = definition['_bsTour'];
  if (definition['auto']) {
    if (!tour.getStep(tour.getCurrentStep()+1)) {
      // don't show sleep periods for the last step
      $('select[name="sleep-period"]').hide();
      $('#btn-later').text('End');
    } else {
      // maintain the sleep value across steps if it has been set
      if (this.sleepPeriod !== undefined && $('select[name="sleep-period"]').length) {
          $('select[name="sleep-period"]').val(this.sleepPeriod);
      }
    }
  }
}


NewFeatureHelpController.prototype._tourTemplate = function(tourId, i, step) {
    let definition = this.tourDefinitions[tourId];
    if (definition['auto']) {
        return `<div class='popover tour'>
      <div class='arrow'></div>
  <h3 class='popover-title'></h3>
  <div class='popover-content'></div>
  <div class='popover-navigation'>
    <button class='btn btn-sm btn-default' data-role='prev'>« Prev</button>
    <button class='btn btn-sm btn-default' data-role='next'>Next »</button>
    <button class='btn btn-sm btn-default' id='btn-later' data-role='end'>Later</button>
    <select name="sleep-period">
    <option value="">Show me ...</option>
    <option value="-1">Never again</option>
    <option value="+5 minutes">In 5 minutes</option>
    <option value="+1 hour">1 hour</option>
    <option value="+1 day">1 day</option>
    <option value="+1 week">1 week</option>
    </select>
  </div>          
  </div>`
    } else {
        return `<div class='popover tour'>
      <div class='arrow'></div>
  <h3 class='popover-title'></h3>
  <div class='popover-content'></div>
  <div class='popover-navigation'>
    <button class='btn btn-sm btn-default' data-role='prev'>« Prev</button>
    <button class='btn btn-sm btn-default' data-role='next'>Next »</button>
    <button class='btn btn-sm btn-default' data-role='end'>End</button>
  </div>          
  </div>`
    }
}
