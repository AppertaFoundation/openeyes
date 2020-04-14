/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

(function (exports) {

    'use strict';

    /**
     * Overlay constructor.
     * @constructor
     * @name OpenEyes.UI.LoadingOverlay
     * @memberOf OpenEyes.UI
     * @example
     * let loadingOverlay = new OpenEyes.UI.LoadingOveraly();
     * loadingOverlay.open();
     * setTimeout(() => loadingOverlay.close(), 1000);
     */
    function LoadingOverlay() {
        this.overlay = $('<div>', {class: 'oe-popup-wrap oe-loading-overlay'});
        this.spinner = $('<div>', {class: 'spinner'});
        this.overlay.append(this.spinner);
    }

   /**
    * Opens (shows) the overlay.
    * @name OpenEyes.UI.LoadingOverlay#open
    * @method
    * @public
    */
    LoadingOverlay.prototype.open = function () {
        $('body').prepend(this.overlay);
    };

    /**
    * Closes (hides) the overlay.
    * @name OpenEyes.UI.LoadingOverlay#close
    * @method
    * @public
    */
    LoadingOverlay.prototype.close = function () {
        $('.oe-loading-overlay').remove();
    };

    exports.LoadingOverlay = LoadingOverlay;

}(OpenEyes.UI));

