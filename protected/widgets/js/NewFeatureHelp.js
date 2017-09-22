function NewFeatureHelpController(splashScreen, tours, downloadLinks) {
  this.tours = [];
  this.splashScreenElements = [];
  this.downloadLinks = downloadLinks;
  this._initTours(tours);
  this._initSplashScreen(splashScreen);
}

NewFeatureHelpController.prototype.showSplashScreen = function() {
  this.endAllTours();
  window.scrollTo(0,0);
  this._showSplashScreen();
}

NewFeatureHelpController.prototype.hideSplashScreen = function() {
  this.endAllTours();
  this._hideSplashScreen();
}

NewFeatureHelpController.prototype._showSplashScreen = function() {
  this.splashScreenElements.forEach(($element) => {
    if ($element.attr('data-showparent') === 'true') {
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
    if ($element.attr('data-showparent') === 'true') {
      $element.parent().css('z-index','');
    }
    $element.css('z-index','');
  });
  $('#help-body-overlay').hide();
  $('#help-header-overlay').hide();
  $('[data-toggle="popover"]').popover('hide');
}

NewFeatureHelpController.prototype.startTour = function(tourName) {
  this.hideSplashScreen();
  this.endAllTours();
  this.tours[tourName].restart();
}

NewFeatureHelpController.prototype.endAllTours = function() {
  for (var tour in this.tours) {
    if (this.tours.hasOwnProperty(tour) && !this.tours[tour].ended()) {
      this.tours[tour].end();
    }
  }
}

NewFeatureHelpController.prototype._initTours = function(tours) {
  for (var tourName in tours) { //tourName is tour id has own but prefixed
    var tourSteps = tours[tourName];
    this.tours[tourName] = new Tour(
      {
        name: tourName,
        backdrop: true,
        steps: tourSteps,
        onEnd: this._tourEnded.bind(this,tourName),
        onStart: this._tourStarted.bind(this,tourName)
      }
    );
  }
}

NewFeatureHelpController.prototype._initSplashScreen = function(splashScreen) {
  splashScreen.forEach((popup) => {
    var $element = $(popup.element);
    $element.attr('title', popup.title);
    for (var data in popup) {
      if (!['element','title'].includes(data)) {
        $element.attr(`data-${data}`,popup[data]);
      }
    }
    this.splashScreenElements.push($element);
  });
}

NewFeatureHelpController.prototype._tourEnded = function(tourName) {
  $('.popover').remove();
}

NewFeatureHelpController.prototype._tourStarted = function(tourName) {
  this.tours[tourName].goTo(0);
  for (var i = 0; i < this.tours[tourName]._options.steps.length; i++) {
    this.tours[tourName].next();
  }
  this.tours[tourName].goTo(0);
}
