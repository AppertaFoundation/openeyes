/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

var OpenEyes = window.OpenEyes || {};
OpenEyes.Util = OpenEyes.Util || {};

/**
 * Extends an object with another objects' properties.
 * @name Object#mixin
 */
if (!Object.prototype.mixin) {
  Object.defineProperty(Object.prototype, 'mixin', {
    value: function(obj) {
      for (var prop in obj) {
        if (obj.hasOwnProperty(prop)) {
          this[prop] = obj[prop];
        }
      }
      return this;
    }
  });
}

/**
 * Extend an objects' prototype with another objects' prototype.
 * @name Function#inherits
 */
if (!Function.prototype.inherits) {
  Object.defineProperty(Function.prototype, 'inherits', {
    value: function(_super, _subProto) {
      this._super = _super;
      this.prototype = Object.create(_super.prototype);
      this.prototype.constructor = this;
      this.prototype.mixin(_subProto);
      return this;
    }
  });
}

/**
 * Function.prototype.bind polyfill for older browsers
 * @name Function.prototype#bind
 */
if (!Function.prototype.bind) {
  Object.defineProperty(Function.prototype, 'bind', {
    value: function(oThis) {

      if (typeof this !== "function") {
        // closest thing possible to the ECMAScript 5 internal IsCallable function
        throw new TypeError("Function.prototype.bind - what is trying to be bound is not callable");
      }

      var aArgs = Array.prototype.slice.call(arguments, 1);
      var fToBind = this;
      var fNOP = function () {};
      var fBound = function () {
        return fToBind.apply((this instanceof fNOP && oThis ? this : oThis),
          aArgs.concat(Array.prototype.slice.call(arguments)));
      };

      fNOP.prototype = this.prototype;
      fBound.prototype = new fNOP();

      return fBound;
    }
  });
}

(function(window) {

  /**
   * Emitter
   * @name Emitter
   * @constructor
   */
  function Emitter() {
    this.events = {};
  }

  /**
   * Adds a new handler (function) for a given event.
   * @name Emitter#on
   * @method
   * @param {string} type - The event type.
   * @param {function} handler - The callback handler for the event type.
   * @returns {this}
   */
  Emitter.prototype.on = function(type, handler) {

    var events = this.events;

    if (!events[type]) {
      events[type] = [];
    }

    events[type].push(handler);

    return this;
  };

  /**
   * Remove a specific handler, or all handlers for a given event.
   * @name Emitter#off
   * @method
   * @param {string} type - The event type.
   * @param {function} [handler] - The callback handler to remove for the given event (optional)
   * @returns {this}
   */
  Emitter.prototype.off = function(type, handler) {

    var events = this.events[type];

    if (events) {

      if (!handler) {
        // Remove all event handlers
        events = [];
      } else {
        // Remove a specific event handler
        events.splice(events.indexOf(handler), 1);
      }

      // If this event handler group is empty then remove it
      if (!events.length) {
        delete this.events[type];
      }
    }

    return this;
  };

  /**
   * Executes all handlers for a given event.
   * @name Emitter#emit
   * @method
   * @param {string} type - The event type.
   * @param {mixed} data - Event data to be passed to all the event handlers.
   * @returns {this}
   */
  Emitter.prototype.emit = function(type, data) {

    var event;
    var events = (this.events[type] || []).slice();

    // First, lets execute all the event handlers
    if (events.length) {
      while ((event = events.shift())) {
        event.call(this, data);
      }
    }

    // Now try trigger a callback handler
    return this.trigger(type, data);
  };

  /**
   * Execute a callback handler for a given event. Callback handlers are stored
   * within the 'options' property of this object, and have the format of 'onEventName'.
   * @name Emitter#trigger
   * @method
   * @param {string} type - The event type.
   * @param {mixed} data - Event data to be passed to all the event handlers.
   * @returns {this}
   */
  Emitter.prototype.trigger = function(type, data) {

    if (!this.options) {
      return;
    }

    var name = 'on' + type.slice(0,1).toUpperCase() + type.slice(1);
    var handler = this.options[name];

    if (handler) {
      handler.call(this, data);
    }

    return this;
  };

  OpenEyes.Util.EventEmitter = Emitter;

}(this));