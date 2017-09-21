function Steps() {
  this.elements = [];
  this.elementsContent = [];
  this.popup = $('.help-popup');
  this.toggles = {overlay: 0b0, tour: 0b0};
  this.buttons = {trigger: $('.help-trigger-btn'), close: $('.help-close'), overlay: $('#help-overlay-btn'), tour: $('#help-tour-btn')};
  this._addListeners();
  this._getBackdrops();
  this.tour = new Tour({backdrop: true, onEnd: this._tourEnd.bind(this), onStart: this._tourStart.bind(this)});
}
Steps.prototype._tourStart = function(tour) {
  this.buttons.tour.removeClass('help-action');
  this.buttons.tour.addClass('help-action-active');
  this.buttons.tour.html('End Tour');
}
Steps.prototype._tourEnd = function(tour) {
  this.buttons.tour.removeClass('help-action-active');
  this.buttons.tour.addClass('help-action');
  this.buttons.tour.html('Take a Tour');
  this.toggles.tour = 0b0;
}
Steps.prototype._addListeners = function () {
  this.buttons.overlay.on('click', () => {this.toggleOverlay(true);});
  this.buttons.tour.on('click', () => {this.toggleTour(true);});
  this.buttons.trigger.on('click',() => {this.togglePopup(true);});
  this.buttons.close.on('click',() => {this.closePopup();});
}
Steps.prototype.openPopup = function () {
  if (this.popup.css('display') === 'none') {
    this.popup.show();
    this.buttons.overlay.trigger('click');
  }
}
Steps.prototype.closePopup = function () {
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
Steps.prototype.togglePopup = function () {
  if (this.popup.css('display') === 'none') {
    this.openPopup();
  } else {
    this.closePopup();
  }
}
Steps.prototype.toggleTour = function (force) {
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
Steps.prototype._startTour = function () {
  this._showLongContent();
  localStorage.clear(); //this is a temporary fix for bug with bootstrap-tour when using it in conjunction with popover.js
  this.tour.start(true);
  this.elements.forEach(() => {
    this.tour.next(); //go through all tour steps then go back to beginning
  }); //as for some reason bootstrap-tourbugs the first time round when using in conjunction with popover.js
  this.tour.goTo(0);
}
Steps.prototype._endTour = function () {
  this.tour.end();
  $('.popover').remove();
}
Steps.prototype._getBackdrops = function () {
  this.header_overlay = $('#help-body-overlay');
  this.body_overlay = $('#help-header-overlay');
}
Steps.prototype.addStep = function (params) {
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
Steps.prototype.addSteps = function (params) {
  params.forEach((el) => {
    this.addStep(el);
  });
}
Steps.prototype._showShortContent = function () {
  this.elementsContent.forEach((el) => {
    $(el.element).attr('data-content',el.shortContent);
  });
}
Steps.prototype._showLongContent = function () {
  this.elementsContent.forEach((el) => {
    $(el.element).attr('data-content',el.longContent);
  });
}
Steps.prototype.toggleOverlay = function (force) {
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
