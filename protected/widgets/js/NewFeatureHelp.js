function NewFeatureHelpController(splashScreen, tours, downloadLinks) {
  this.tours = [];
  this.splashScreen = [];
  this.downloadLinks = [];

  this._initTours(tours);
  this._initSplashScreen(splashScreen);
}
NewFeatureHelpController.prototype._initTours = function(tours) {
  for (var tourName in tours) { //tourName is tour id
    var tourSteps = tours[tourName];
    this.tours[tourName] = new Tour(
      {
        name: tourName,
        backdrop: true,
        steps: tourSteps,
        onEnd: this._tourEnd.bind(this,tourName),
        onStart: this._tourStart.bind(this,tourName)
      }
    );
  }
}
NewFeatureHelpController.prototype._initSplashScreen = function(splashScreen) {
  console.log(splashScreen);
  //add attributes to all elements required may interfer with rest and cause similar bug so use old fix
}
NewFeatureHelpController.prototype._tourEnd = function(tourName) {
  console.log(tourName);
  console.log("has ended");
}
NewFeatureHelpController.prototype._tourStart = function(tourName) {
  console.log(tourName);
  console.log("has started");
}
