(function (exports, Util, EventEmitter) {
    function NavBtnPopup(id, $btn, $content) {
        this.id = id;
        this.eventObj = $btn;
        this.button = $btn;
        this.content = $content
        this.useMouseEvents = false;
        this.isGrouped = false; 		// e.g. patient popups
        this.groupController = null;
        this.isFixed = false;
        this.latchable = false;
        this.isLatched = false;
        this.css = {
            active: 'active', 	// hover over button or popup
            open: 'open', 		// clicked (latched)
        };

        this.init();
    }

    Util.inherits(EventEmitter, NavBtnPopup);

    NavBtnPopup.prototype.init = function () {
        let popup = this;
        // Events
        popup.eventObj.click(function (e) {
            console.log(this);
            console.log(popup);
            e.stopPropagation();
            // use button class as boolean
            popup.changeContent(popup.button.hasClass(popup.css.open));
        }).mouseenter(function () {
            if (popup.isLatched) return;
            popup.button.addClass(popup.css.active);
            if (popup.useMouseEvents) {
                popup.show();
            }
        }).mouseleave(function () {
            if (popup.isLatched) return;
            popup.button.removeClass(popup.css.active);
            if (popup.useMouseEvents) {
                popup.hide();
            }
        });
    }

    /**
     provide a way for shortcuts to re-assign
     the Events to the DOM wrapper
     **/
    NavBtnPopup.prototype.changeContent = function (isOpen) {
        let popup = this;
        if (popup.isFixed) return; // if popup is fixed

        if (popup.latchable) {
            if (popup.isLatched) {
                if (popup.isGrouped) {
                    popup.groupController.closeAll();
                }
                popup.unlatch();
            } else {
                if (popup.isGrouped) {
                    popup.groupController.closeAll();
                }
                popup.latch()
            }
        } else if (isOpen) {
            popup.hide();
        } else {
            if (popup.isGrouped) {
                popup.groupController.closeAll();
            }
            popup.show();
        }
    };

    NavBtnPopup.prototype.show = function () {
        let popup = this;
        popup.button.addClass(popup.css.open);
        popup.content.show();
        if (popup.useMouseEvents && !popup.isFixed) popup.addContentEvents();
    };

    NavBtnPopup.prototype.hide = function () {
        let popup = this;
        this.button.removeClass(popup.css.open);
        this.content.hide();
    };

    /**
     Enhance $content behaviour for non-touch users
     Allow mouseLeave to close $content popup
     **/
    NavBtnPopup.prototype.addContentEvents = function () {
        let popup = this;
        this.content.mouseenter(function () {
            $(this).off('mouseenter'); // clean up
            $(this).mouseleave(function () {
                $(this).off('mouseleave'); // clean up
                popup.hide();
            });
        });
    };

    /**
     DOM structure for the Shortcuts dropdown list is different
     Need to shift the events to the wrapper DOM rather than the $btn
     **/
    NavBtnPopup.prototype.useWrapperEvents = function (DOMwrapper) {
        let popup = this;
        popup.eventObj.off('click mouseenter mouseleave');
        popup.eventObj = DOMwrapper;
        popup.css.open = popup.css.active; // wrap only has 1 class
        popup.useMouseEvents = true;
        popup.init(); // re initiate with new eventObj
    };

    /**
     Hotlist is structured like Shortcuts but requires a different
     behaviour, it requires enhanced behaviour touch to lock it open!
     **/
    NavBtnPopup.prototype.useAdvancedEvents = function ($wrapper) {
        let popup = this;
        popup.eventObj = $wrapper;
        //click needs to open/close OR if mouseEvents are working, lock open
        popup.button.click(function (e) {
            e.stopPropagation();
            if (!popup.isFixed) {
                if (popup.isLatched) {
                    // if open it
                    popup.unlatch();
                    popup.hide();
                } else {
                    popup.latch();
                }
            }
        });

        // enhance for Mouse/Track users
        popup.eventObj
            .mouseenter(function () {
                popup.button.addClass(popup.css.active);
                popup.show();
            })
            .mouseleave(function () {
                popup.button.removeClass(popup.css.active);
                if (!popup.isLatched && !popup.isFixed) {
                    popup.hide();
                }
            });
    }

    /**
     Activity Panel needs to be fixable when the browsers is wide enough
     (but not in oescape mode)
     **/
    NavBtnPopup.prototype.fixed = function (b) {
        let popup = this;
        popup.isFixed = b;
        if (b) {
            this.content.off('mouseenter mouseleave');
            popup.show();
        } else {
            popup.hide();
        }
    }

    NavBtnPopup.prototype.latch = function () {
        let popup = this;
        if (popup.groupController) {
            popup.groupController.lockAll();
        }
        popup.isLatched = true;
        popup.show();
        this.content.off('mouseenter mouseleave');
    }

    NavBtnPopup.prototype.unlatch = function () {
        let popup = this;
        if (popup.groupController) {
            popup.groupController.unlockAll();
        }
        popup.isLatched = false;
        popup.hide();
        popup.button.removeClass(popup.css.active);
    }

    /**
     Group popups to stop overlapping
     **/
    NavBtnPopup.prototype.inGroup = function (controller) {
        let popup = this;
        popup.isGrouped = true;
        popup.groupController = controller;
    }

  exports.NavBtnPopup = NavBtnPopup;
}(OpenEyes.UI, OpenEyes.Util, OpenEyes.Util.EventEmitter));