(function (exports, Util, EventEmitter) {
    var latchSingleton;

    function NavBtnPopup(id, $btn, $content) {

        var popup = this;

        // private
        this.id = id;
        this.eventObj = $btn;
        this.useMouseEvents = false;
        this.isGrouped = false; 		// e.g. patient popups
        this.groupController = null;
        this.isFixed = false;
        this.latchable = false;
        this.isLatched = false;
        this.css = {
            active: 'active', 	// hover
            open: 'open' 		// clicked
        };
        init(); // all are initiated but useWrapperEvents modifies the eventObj then re-initiates

        function init() {
            // Events
            popup.eventObj.click(function (e) {
                e.stopPropagation();
                // use $btn class as boolean
                changeContent($btn.hasClass(popup.css.open));
            }).mouseenter(function () {
                if (popup.isLatched) return;
                //if there is a latched popup, don't bother showing this (potentially over/under)
                if (!latchSingleton) {
                    $btn.addClass(popup.css.active);
                    if (popup.useMouseEvents) {
                        show();
                    }
                }
            }).mouseleave(function () {
                if (popup.isLatched) return;
                $btn.removeClass(popup.css.active);
                if (popup.useMouseEvents) {
                    hide();
                }
            });
        }

        /**
         public methods
         **/
        this.init = init;
        this.hide = hide;
        this.useWrapper = useWrapperEvents;
        this.fixed = fixed;
        this.inGroup = inGroup;
        this.show = show;
        this.hide = hide;
        this.latch = latch;
        this.unlatch = unlatch;

        /**
         provide a way for shortcuts to re-assign
         the Events to the DOM wrapper
         **/


        function changeContent(isOpen) {
            if (popup.isFixed) return; // if popup is fixed

            if (popup.latchable) {
                if (popup.isLatched) {
                    if (popup.isGrouped) {
                        popup.groupController.closeAll();
                    }
                    popup.unlatch();
                } else {
                    if (!latchSingleton) {
                        if (popup.isGrouped) {
                            popup.groupController.closeAll();
                        }
                        popup.latch();
                    }
                }
            } else if (isOpen) {
                popup.hide();
            } else {
                if (popup.isGrouped) {
                    popup.groupController.closeAll();
                }
                popup.show();
            }
        }

        function show() {
          $btn.addClass(popup.css.open);
          $content.show();
          if (popup.useMouseEvents && !popup.isFixed) {
            addContentEvents();
          }

          //handle popups extending off screen

          if ($content.closest('#oe-patient-details').length > 0) {
            var $main_event = $('.open-eyes');
            var mainBox = $main_event[0].getBoundingClientRect();
            var contentBox = $content[0].getBoundingClientRect();
            var parentBox = $content[0].closest('#oe-patient-details').getBoundingClientRect();
            var boundTo = 'bottom';

            //moved the popup to above rather than below it's parent if it will go beyond the bottom of the main div
            if (parentBox.bottom > mainBox.bottom / 2) {
              $content[0].style.top = (parentBox.top - contentBox.height) + "px";
              boundTo = 'top';
            } else {
              $content[0].style.top = parentBox.bottom + "px";
            }
            $content[0].style.left = parentBox.left + "px";

            $main_event.unbind("scroll");

            function movePopupWithScroll(e) {
              if (boundTo === 'top') {
                $content[0].style.top = (
                  $content[0].closest('#oe-patient-details').getBoundingClientRect().top -
                  $content[0].getBoundingClientRect().height
                ) + "px";
              } else {
                $content[0].style.top = $content[0].closest('#oe-patient-details').getBoundingClientRect().bottom + "px";
              }
            }
            $main_event.bind("scroll", movePopupWithScroll);
          }
        }

        function hide() {
            $btn.removeClass(popup.css.open);
            $content.hide();
        }

        /**
         Enhance $content behaviour for non-touch users
         Allow mouseLeave to close $content popup
         **/
        function addContentEvents() {
            $content.mouseenter(function () {
                $(this).off('mouseenter'); // clean up
                $(this).mouseleave(function () {
                    $(this).off('mouseleave'); // clean up
                    popup.hide();
                });
            });
        }

        /**
         DOM structure for the Shortcuts dropdown list is different
         Need to shift the events to the wrapper DOM rather than the $btn
         **/
        function useWrapperEvents(DOMwrapper) {
            popup.eventObj.off('click mouseenter mouseleave');
            popup.eventObj = DOMwrapper;
            popup.css.open = popup.css.active; // wrap only has 1 class
            popup.useMouseEvents = true;
            popup.init(); // re initiate with new eventObj
        }

        /**
         Activity Panel needs to be fixable when the browsers is wide enough
         (but not in oescape mode)
         **/
        function fixed(b) {
            popup.isFixed = b;
            if (b) {
                $content.off('mouseenter mouseleave');
                popup.show();
            } else {
                popup.hide();
            }
        }

        function latch() {
            latchSingleton = 'locked';
            if (popup.groupController) {
                popup.groupController.lockAll();
            }
            popup.isLatched = true;
            popup.show();
            $content.off('mouseenter mouseleave');
        }

        function unlatch() {
            latchSingleton = null;
            if (popup.groupController) {
                popup.groupController.unlockAll();
            }
            popup.isLatched = false;
            popup.hide();
            $content.on('mouseenter mouseleave');
            $btn.removeClass(popup.css.active);
        }

        /**
         Group popups to stop overlapping
         **/
        function inGroup(controller) {
            popup.isGrouped = true;
            popup.groupController = controller;
        }
    }

    Util.inherits(EventEmitter, NavBtnPopup);

    exports.NavBtnPopup = NavBtnPopup;
}(OpenEyes.UI, OpenEyes.Util, OpenEyes.Util.EventEmitter));