/**
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
(function(exports) {

    'use strict';

    /**
     * OpenEyes DOM module
     * @namespace OpenEyes.UI.DOM
     * @memberOf OpenEyes
     */
    const DOM = {};

    /**
     * Usage:
     * ready(() => { console.log("ready") });
     */
    if (typeof ready !== 'function') {
        window.ready = function (fn) {
            if (document.readyState !== 'loading') {
                fn();
            } else {
                document.addEventListener('DOMContentLoaded', fn);
            }
        };
    }

    /**
     * Creates DOM element
     *
     * Usage: OpenEyes.UI.DOM.createElement('div', { 'class': 'js-something' });
     *
     * @param name
     * @param attributes
     * @returns {*}
     */
    DOM.createElement = function(name, attributes = {}) {
        const $input = document.createElement(name);

        for (let attr in attributes) {
            if( attributes.hasOwnProperty(attr) ) {
                $input.setAttribute(attr, attributes[attr]);
            }
        }

        return $input;
    };

    /**
     * Adds event listener to object, optionally with subselector / like .on('click', 'a', () => {} )
     *
     * Usage: OpenEyes.UI.DOM.addEventListener($wrapper, 'click', 'a', (e) => { console.log(e.target); });
     *
     * @param onObject
     * @param type
     * @param subselector
     * @param listener
     * @param options
     */
    DOM.addEventListener = function(onObject, type, subselector, listener, options) {
        onObject.addEventListener(type, function(e) {
            if (subselector) {
                for (let target = e.target; target && target !== this; target = target.parentNode) {
                    if (target.matches(subselector)) {
                        listener.call(target, e);
                        break;
                    }
                }
            } else {
                listener.call(e.target, e);
            }
        }, options);
    };

    /**
     * Triggers an event
     *
     * Usage: OpenEyes.UI.DOM.trigger($element, 'click');
     *
     * @param element
     * @param event_type
     */
    DOM.trigger = function(element, event_type) {
        const event = new Event(event_type, {
            bubbles: true,
            cancelable: true,
        });
        element.dispatchEvent(event);
    };

    exports.DOM = DOM;

}(this.OpenEyes.UI));
