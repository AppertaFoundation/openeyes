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

OpenEyes.AdminSidebar = OpenEyes.AdminSidebar || {};

(function(exports) {

    'use strict';

    function Sidebar(options) {
        this.options = $.extend(true, {}, Sidebar._defaultOptions, options);
        this.$sidebar = $(this.options.containerSelector);
        this.groupSelector = this.options.groupSelector;
        this.toggleAllSelector = this.options.toggleAllSelector;
        this.iconSelector = this.options.iconSelector;
        this.toggleIconClasses = this.options.closeIconClassName + ' ' + this.options.openIconClassName;
        this.closeIconClassName = this.options.closeIconClassName;
        this.openIconClassName = this.options.openIconClassName;

        this.initTriggers();
    }

    Sidebar._defaultOptions = {
        containerSelector: 'nav.oe-full-side-panel',
        toggleAllSelector: '.js-groups',
        iconSelector: '.oe-i',
        groupSelector: '.collapse-group',
        closeIconClassName: 'minus',
        openIconClassName: 'plus'
    };

      Sidebar.prototype.initTriggers = function(){
        var controller = this;

        this.$sidebar.on('click', this.toggleAllSelector + ' ' + this.iconSelector, function(){
            let action = $(this).hasClass(controller.openIconClassName) ? 'expand' : 'collapse';
            let groups = controller.$sidebar.find(controller.groupSelector);
            $.each(groups, function(i, group){
                controller.toggleGroup($(group), action);
            });
        });

        this.$sidebar.on('click', this.groupSelector, function(e){
            // if child element clicked we do not want to close the group
            if (e.target.tagName === 'A'){
                return;
            }
            let action = $(this).find(controller.iconSelector).hasClass(controller.openIconClassName) ? 'expand' : 'collapse';
            controller.toggleGroup($(this).closest(controller.groupSelector), action);
        });
    };

    Sidebar.prototype.toggleGroup = function($group, state){
        let controller = this;
        let $ul = $group.find('ul');

        if(state === 'collapse'){
            $ul.slideUp(400, function(){
                $group.find(controller.iconSelector).addClass(controller.openIconClassName);
                $group.find(controller.iconSelector).removeClass(controller.closeIconClassName);
                $group.data('collapse', 'collapsed');
            });
        } else {
            $ul.slideDown(400, function(){
                $group.find(controller.iconSelector).addClass(controller.closeIconClassName);
                $group.find(controller.iconSelector).removeClass(controller.openIconClassName);
                $group.data('collapse', 'expanded');
            });
        }
    };

    exports.Sidebar = Sidebar;
})(OpenEyes.AdminSidebar, OpenEyes.Util);

$(document).ready(function(){
    window.adminSidebar = new OpenEyes.AdminSidebar.Sidebar();
});