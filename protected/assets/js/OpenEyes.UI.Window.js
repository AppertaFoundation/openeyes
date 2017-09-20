/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

(function (exports) {
    /**
     * OpenEyes UI Widgets namespace
     * @namespace OpenEyes.UI.Window
     */
    exports.Window = {
        /**
         * Function for handling opening link in a new targeted window with callback functions
         * to handle popup blocking.
         *
         * @param urlToOpen
         * @param targetName
         * @param successCallback
         * @param errorCallback
         */
        createNewWindow: function(urlToOpen, targetName, successCallback, errorCallback) {
            if (successCallback === undefined) {
                successCallback = function(popup) { popup.focus();}
            }
            if (errorCallback === undefined) {
                errorCallback = function() {alert("Pop-up Blocker is enabled! Please add this site to your exception list.");}
            }
            var popup_window=window.open(urlToOpen,targetName,"toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, copyhistory=yes");
            try {
                popup_window.focus();
                successCallback(popup_window);
            }
            catch (e) {
                errorCallback();
            }
        }
    };


}(this.OpenEyes.UI));
