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
var OpenEyes = OpenEyes || {};

OpenEyes.UI = OpenEyes.UI || {};

(function(exports) {
    function Sidebar(element, options) {
        this.$el = $(element);
        // store the sidebar controller on the element it's controlling.
        this.$el.data('sidebar', this);
        this.options = $.extend(true, {}, Sidebar._defaultOptions, options);
        this.initialise();
    }

    Sidebar._defaultOptions = {
        showHelpOnce: true,
        scrollTip: 'scroll down',
        minimumHeight: 500
    };

    Sidebar.prototype.initialise = function()
    {
        var self = this;

        self.showHelp = true;

        self.$el.scroll(function() {
            if (self.showHelp && self.options.showHelpOnce) {
                self.showHelp = false;
                self.showHelp = false;
            }
        });
    };

    Sidebar.prototype.checkSideNavHeight = function()
    {
        var self = this;
        furniture = $('.oe-header').outerHeight()  + $('#patient-alert-patientticketing').outerHeight();
        h = window.innerHeight - furniture - $('.sidebar-header').outerHeight();
        if(h < self.options.minimumHeight)
            h = self.options.minimumHeight;
        self.$el.height(h+'px');
        $('.container.content').css({'min-height':h+50+'px'});
    };

    exports.Sidebar = Sidebar;
}(OpenEyes.UI));
