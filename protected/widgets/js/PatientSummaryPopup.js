/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

(function (exports) {

    'use strict';

    var container;
    var popup;
    var trackedHeight;
    var popupOverflow;
    var buttons;
    var helpHint;

    var stuck = false;
    var sticky = false;
    var hideTimer = 0;
    var hoverTimer = 0;
    var needsScroll = undefined;

    function update() {
        popup.add(buttons).add(helpHint).trigger('update');
    }

    function init() {
        container = $('#patient-popup-container');
        popup = $('#patient-summary-popup');
        popupOverflow = popup.find('.oe-popup-overflow');
        buttons = container.find('.toggle-patient-summary-popup');
        helpHint = popup.find('.help-hint');

        // Popup custom events.
        popup.on({
            update: function() {
                popup.trigger(stuck ? 'show' : 'hide');
            },
            show: function() {
                updateScrollingCss();
                if (popup.hasClass('show')) return;
                clearTimeout(hideTimer);
                popup.show();

                if (needsScroll === undefined) {
                    setScrollingProperties();
                    updateScrollingCss();
                }

                // Re-define the transitions on the popup to be none.
                popup.addClass('clear-transition');
                // Trigger a re-flow to reset the starting position of the transitions, now
                // existing transitions will be removed.
                popup[0].offsetWidth;
                // Add the initial transition definitions back.
                popup.removeClass('clear-transition');
                // We can now animate in from the initial starting point.
                popup.addClass('show');
            },
            hide: function() {
                clearTimeout(hideTimer);
                popup.removeClass('show');
                // We want the popup to animate out before being hidden.
                hideTimer = setTimeout(popup.hide.bind(popup), 250);
            }
        });

        // Help hint custom events.
        helpHint.on({
            update: function() {
                var text = helpHint.data('text')[ stuck ? 'close' : 'lock' ];
                helpHint.text(text[sticky ? 'short' : 'full']);
            }
        });

        // Button events.
        buttons.on({
            update: function() {
                var button = $(this);
                var showIcon = button.data('show-icon');
                var hideIcon = button.data('hide-icon');
                if (showIcon && hideIcon) {
                    button
                        .removeClass(showIcon + ' ' + hideIcon)
                        .addClass(stuck ? hideIcon : showIcon);
                }

            },
            click: function() {
                stuck = !stuck;
                updateScrollingCss();
                update();
            }
        });

        // We add these mouse events on the container so that the popup does not
        // hide when hovering over the popup contents.
        container.on({
            mouseenter: function() {
                updateScrollingCss();
                clearTimeout(hoverTimer);
                // We use a timer to prevent the popup from displaying unintentionally.
                hoverTimer = setTimeout(popup.trigger.bind(popup, 'show'), 100);
            },
            mouseleave: function() {
                clearTimeout(hoverTimer);
                if (!stuck) {
                    hoverTimer = setTimeout(popup.trigger.bind(popup, 'hide'), 100);
                }
            }
        });
    }

    function setScrollingProperties() {
        // FIXME: this ain't working cos the limit style is maxing the height. Needs thought.
        if (trackedHeight === undefined) {
            trackedHeight = popupOverflow.height();
        }
        if (trackedHeight > 415) {
            popupOverflow.addClass('limit');
            needsScroll = true;
        } else {
            popupOverflow.removeClass('limit');
            needsScroll = false;
        }
    }

    function updateScrollingCss() {
        if (needsScroll) {
                popupOverflow.addClass('scroll');
            }
        }

    /**
     * This is a naive method that simply adds to the recorded height
     * to determine whether the scrolling class is required when content
     * is expanded by external controllers.
     *
     * Tracking whether the height should be added or not is the responsibility
     * of the external controllers.
     *
     * @param pixels
     */
    function addHeight(pixels) {
        trackedHeight+=pixels;
        setScrollingProperties();
        updateScrollingCss();
    }

    function refresh(patientId) {
        if (!patientId) {
            throw new Error('Patient id is required');
        }
        $.ajax({
            type: 'GET',
            url: '/patient/summarypopup/' + patientId
        }).done(function(data) {
            $('#patient-popup-container').replaceWith(data);
            init();
            update();
        });
    }

    // Init on page load.
    $(init);

    // Public API
    exports.PatientSummaryPopup = {
        refresh: refresh,
        addHeight: addHeight
    };

}(this.OpenEyes.UI.Widgets));