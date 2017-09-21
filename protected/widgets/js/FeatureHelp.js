/*
TODO
This controller needs to be updated so that it supports
the changes made to the FeatureHelp Widget.

Namely,

The FeatureHelp widget now takes in 3 parameters:
* Array of tours with names and order (tour steps array as it is in PHP),
* Overlay/Splash screen steps (tour steps array)(currently uses contentLess),
* Array of PDF links

To adjust FeatureHelpController to cope this the changes:
* The constructor takes in 3 parameters
  Array of tours (tour steps in JSON),
  Splash screen (tour steps in JSON),
  Array of PDF links (strings in JSON)
* The constructor must then use the array of tours to instatiate
  the tours (note: conflict may occur and this needs to be handled
  i.e. same element in multiple tours)
* Each tour is stored in the array of Tour objects (instance attribute)
* Each tour object has an associated toggle/state which indicates whether
  the tour is currently being run
* Each tour must be given callback functions for start and end so that
  the toggle/state can be updated for this controller and in case the
  bug occurs which is fixed by running through the tour once programmaticially
* The constructor must also add the relevant data attributes the elements for the
  splash screen / overlay as well as a toggle for the splash screen / overlay
* The constructor must set up event listerners for each of now dynamically
  generated buttons (take a tour {{tour_name}}, show help overlay, download
  a PDF {{pdf_name}}) ensuring to bind the context (i.e. this) to the button
  or use data attributes on the buttons to decide what to do (i.e. start specific tour)
* Write a function that attaches a click event listener to all buttons in
  in the help popup and then use the contents/props/attr of the buttons to execute
  the write code - This function should ensure that only 1 button can be active at once

FeatureHelp used to (Old widget) take in 1 parameter:
* a single tour (stour steps as it is in PHP)
This was fine as there was only 1 tour and 1 splash screen
which shared the same steps and the splash screen used reduced content
(contentLess)
Now the tour and splash screen are independnent and if they are the same
must be entered twice





*/

function FeatureHelpController() {
  this.elements = [];
  this.elementsContent = [];
  this.popup = $('.help-popup');
  this.toggles = {overlay: 0b0, tour: 0b0};
  this.buttons = {trigger: $('.help-trigger-btn'), close: $('.help-close'), overlay: $('#help-overlay-btn'), tour: $('#help-tour-btn')};
  this._addListeners();
  this._getBackdrops();
  this.tour = new Tour({backdrop: true, onEnd: this._tourEnd.bind(this), onStart: this._tourStart.bind(this)});
}
FeatureHelpController.prototype._tourStart = function(tour) {
  this.buttons.tour.removeClass('help-action');
  this.buttons.tour.addClass('help-action-active');
  this.buttons.tour.html('End Tour');
}
FeatureHelpController.prototype._tourEnd = function(tour) {
  this.buttons.tour.removeClass('help-action-active');
  this.buttons.tour.addClass('help-action');
  this.buttons.tour.html('Take a Tour');
  this.toggles.tour = 0b0;
}
FeatureHelpController.prototype._addListeners = function () {
  this.buttons.overlay.on('click', () => {this.toggleOverlay(true);});
  this.buttons.tour.on('click', () => {this.toggleTour(true);});
  this.buttons.trigger.on('click',() => {this.togglePopup(true);});
  this.buttons.close.on('click',() => {this.closePopup();});
}
FeatureHelpController.prototype.openPopup = function () {
  if (this.popup.css('display') === 'none') {
    this.popup.show();
    this.buttons.overlay.trigger('click');
  }
}
FeatureHelpController.prototype.closePopup = function () {
  if (this.popup.css('display') !== 'none') {
    $('.help-popup').hide();
    if (this.toggles.overlay) {
      this.toggleOverlay();
    }
    if (this.toggles.tour) {
      this.toggleTour();
    }
  }
}
FeatureHelpController.prototype.togglePopup = function () {
  if (this.popup.css('display') === 'none') {
    this.openPopup();
  } else {
    this.closePopup();
  }
}
FeatureHelpController.prototype.toggleTour = function (force) {
  if (!force && this.toggles.overlay) {
    return;
  } else if (this.toggles.overlay) {
    this.toggleOverlay();
  }
  if (this.toggles.tour = ~this.toggles.tour) {
    this._startTour();
  } else {
    this._endTour();
  }
}
FeatureHelpController.prototype._startTour = function () {
  /*
  This is a temporary fix for bug with bootstrap-tour when using it in conjunction with popover.js
  go through all tour steps then go back to beginning
  as for some reason bootstrap-tour is bugged the first time round when using in conjunction with popover.js
  */
  this._showLongContent();
  localStorage.clear();
  this.tour.start(true);
  this.elements.forEach(() => {
    this.tour.next();
  });
  this.tour.goTo(0);
}
FeatureHelpController.prototype._endTour = function () {
  this.tour.end();
  $('.popover').remove();
}
FeatureHelpController.prototype._getBackdrops = function () {
  this.header_overlay = $('#help-body-overlay');
  this.body_overlay = $('#help-header-overlay');
}
FeatureHelpController.prototype.addStep = function (params) {
  this.tour.addStep(params);
  const $this = $(params.element);
  $this.attr('data-toggle','popover');
  $this.attr('title',params.title);
  $this.attr('data-content',params.contentLess);
  $this.attr('data-trigger',"manual");
  $this.attr('data-placement',params.placement ? params.placement : "auto right");
  if (params.showParent) {
    this.elements.push($this.parent());
  } else {
    this.elements.push($this);
  }
  this.elementsContent.push({element: params.element, shortContent: params.contentLess, longContent: params.content});
};
FeatureHelpController.prototype.addSteps = function (params) {
  params.forEach((el) => {
    this.addStep(el);
  });
}
FeatureHelpController.prototype._showShortContent = function () {
  this.elementsContent.forEach((el) => {
    $(el.element).attr('data-content',el.shortContent);
  });
}
FeatureHelpController.prototype._showLongContent = function () {
  this.elementsContent.forEach((el) => {
    $(el.element).attr('data-content',el.longContent);
  });
}
FeatureHelpController.prototype.toggleOverlay = function (force) {
  if (!force && this.toggles.tour) {
    return;
  } else if (this.toggles.tour) {
    this.toggleTour();
  }
  if (this.toggles.overlay = ~this.toggles.overlay) {
    this._showShortContent();
    window.scrollTo(0,0);
    this.buttons.overlay.html('Hide Help Overlay');
    this.buttons.overlay.removeClass('help-action');
    this.buttons.overlay.addClass('help-action-active');
    this.elements.forEach(function(elem){
      elem.css('z-index', 161);
    });
    this.header_overlay.show();
    this.body_overlay.show();
    $('[data-toggle="popover"]').popover('show');
  } else {
    this.buttons.overlay.html('Show Help Overlay');
    this.buttons.overlay.removeClass('help-action-active');
    this.buttons.overlay.addClass('help-action');
    this.elements.forEach(function(elem){
      elem.css('z-index', '');
    });
    $('[data-toggle="popover"]').popover('hide');
    this.header_overlay.hide();
    this.body_overlay.hide();
  }
}
