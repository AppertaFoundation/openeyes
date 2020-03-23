var OpenEyes = OpenEyes || {};

OpenEyes.UI = OpenEyes.UI || {};

(function (exports) {

  function NavBtnSidebar(options) {
    this.options = $.extend(true, {}, NavBtnSidebar._defaultOptions, options);
    this.create();
  }

  NavBtnSidebar._defaultOptions = {
    nav_button_selector: '#js-nav-hotlist-btn',
    panel_selector: '#js-hotlist-panel',
  };

  NavBtnSidebar.prototype.create = function () {
    var self = this;
    this.$nav_button = $(this.options.nav_button_selector);
    this.$panel = $(this.options.panel_selector);
    this.fixable = this.$nav_button.data('fixable');
    this.latched = false;

    this.autoHideWidthPixels = 1800;

    $(window).resize(function () {
      self.onBrowserSizeChange();
    });
    self.onBrowserSizeChange();

    this.$nav_button.on('click', function () {
      if (self.isFixable()) {
        return;
      }
      self.latched = !self.latched;
      self.toggle(self.latched);
    });

    this.$nav_button.on('mouseover', function () {
      self.show();
    });

    this.$nav_button.on('mouseout', function () {
    	let mouseIsOverPanel = self.$panel.filter(':hover').length;
      if (!self.isFixable() && !self.latched && !mouseIsOverPanel) {
        self.hide();
      }
    });

    this.$panel.on('mouseleave', function(){
    	let mouseIsOverBtn = self.$nav_button.filter(':hover').length;
			if (!self.isFixable() && !self.latched && !mouseIsOverBtn) {
				self.hide();
			}
		})
  };

  NavBtnSidebar.prototype.show = function () {
    this.toggle(true);
  };

  NavBtnSidebar.prototype.hide = function () {
    this.toggle(false);
  };

  NavBtnSidebar.prototype.toggle = function (show) {
    this.$panel.toggle(show);
    this.$nav_button.toggleClass('active', show);
  };

  NavBtnSidebar.prototype.onBrowserSizeChange = function () {
    if (this.latched) {
      return;
    }

    if ($(window).width() > this.autoHideWidthPixels) {
      this.toggle(this.isFixable());
    } else {
      this.hide();
    }
  };

  NavBtnSidebar.prototype.isFixable = function () {
    return this.$nav_button.data('fixable') && $(window).width() > this.autoHideWidthPixels;
  };

  exports.NavBtnSidebar = NavBtnSidebar;

}(OpenEyes.UI));