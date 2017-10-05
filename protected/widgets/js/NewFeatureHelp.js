function NewFeatureHelpController(splashScreen, tours, downloadLinks) {
  this.tourDefinitions = {};
  this.autoTours = [];
  this.splashScreenElements = [];
  this.downloadLinks = downloadLinks ? downloadLinks : [];
  this._initTours(tours);
  this._initSplashScreen(splashScreen);
  if (Object.keys(this.tourDefinitions).length
      || this.downloadLinks.length
      || this.splashScreenElements.length) {
    // we have some help to show
    this._addListeners();
    this._autoStart();
  } else {
    $('.help-trigger-btn').hide();
  }

}

NewFeatureHelpController.prototype._addListeners = function() {
  $('.help-trigger-btn').on('click',this.togglePopup.bind(this));
  $('#help-splash-screen-btn').on('click',this.toggleSplashScreen.bind(this));
  $('.help-action-tour').on('click',this._toggleTour.bind(this));
  $('.help-close').on('click',this.togglePopup.bind(this));
}

NewFeatureHelpController.prototype._autoStart = function() {
  if (this.autoTours.length) {
    this.startTour(this.autoTours.pop());
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
    var definition = tours[idx];
    var tourId = definition['id'];
    if (definition['auto']) {
        this.autoTours.push(tourId);
    }
    this.tourDefinitions[tourId] = definition;
    this.tourDefinitions[tourId]['_bsTour'] = new Tour(
      {
        name: tourId,
        backdrop: true,
        storage: window.localStorage,
        steps: definition['steps'],
        onEnd: this._tourEnded.bind(this,tourId),
        onStart: this._tourStarted.bind(this,tourId),
        afterSetState: this._setTourState.bind(this)
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
  $button.html(`Start ${definition['name']}`);
  $button.removeClass('help-action-active');
  $button.addClass('help-action');
}

NewFeatureHelpController.prototype._tourStarted = function(tourId) {
  var definition = this.tourDefinitions[tourId];
  definition['_bsTour'].goTo(0);

  let $button = $(`#help-tour-name-${tourId}`);
  $button.html(`End ${definition['name']}`);
  $button.removeClass('help-action');
  $button.addClass('help-action-active');
}

NewFeatureHelpController.prototype._setTourState = function(key, value) {
  if (key.match(/_end$/) && value === 'yes') {
    let id = key.substring(0, key.length-4);
    console.log(id);
    $.post(
        '/FeatureTour/complete?id=' + id,
        {YII_CSRF_TOKEN: YII_CSRF_TOKEN}
    );
  }
}
