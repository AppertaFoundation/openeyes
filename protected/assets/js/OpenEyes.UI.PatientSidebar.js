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

(function(exports) {
    /**
     * PatientSidebar constructor. The PatientSidebar ......
     *
     * @param options
     * @constructor
     */
    function PatientSidebar(element, options) {
        this.$element = $(element);
        this.$element.data('patient-sidebar', this);
        this.options = $.extend(true, {}, PatientSidebar._defaultOptions, options);
        this.create();
    }

    /**
     * _defaultOptions
     *
     *  patient_sidebar_json        json string     JSON string of the sidebar elements
     *  element_container_selector  string
     *  tree_id                     string          CSS CLass ID of where the built sidebar will go
     */
    PatientSidebar._defaultOptions = {
        patient_sidebar_json: {},
        element_container_selector: '.js-active-elements',
        tree_id: '',
        scroll_selector: 'div.oe-scroll-wrapper'
    };

    /**
     * Simple error message wrapper
     * @param msg
     */
    PatientSidebar.prototype.error = function(msg)
    {
        console.log('PatientSidebar ERROR: ' + msg);
    };

    /**
     *  Main code that builds and updates the tree
     */
    PatientSidebar.prototype.create = function() {
        var self = this;

        var $scrollElement = self.$element.find(self.options.scroll_selector);
        if ($scrollElement.length) {
            // if the scrollbar controller is in place, then when we have changed the content
            // we want to trigger the resize event, as the element list may be longer than the
            // original contents.
            this.sidebarController = $scrollElement.data('sidebar');
        }

        var $realContainer = self.$element.find('.all-panels');

        var $children = $realContainer.children();
        var $newContent = $('<div class="oe-event-sidebar-edit"><ul class="oe-element-list"></ul></div>');
        $realContainer.attr('id', self.options.tree_id);
        self.$element = $newContent.find('ul');

        self.openElements();

        self.errorElements();

        self.parseJSON();

        self.buildTree();

        $realContainer.children().remove();
        $realContainer.append($newContent);

        if (this.sidebarController) {
            this.sidebarController.checkSideNavHeight();
        }

        self.$elementContainer = $(document).find(self.options.element_container_selector);

        // couple of hooks to keep the menu in sync with the elements on the page.
        self.$elementContainer.on('click', '.js-remove-element', function(e) {
            self.removeElememt(e.target);
        });

        self.$elementContainer.on('click', '.js-remove-child-element', function(e) {
            self.removeElememt(e.target);
        });

        // expand or collapse the menu for the given menu item
        self.$element.on('click', 'a .icon', function(e) {
            e.preventDefault();
            e.stopPropagation();

            var li = $(this).parent().parent();
            if($(this).hasClass('expand')){
                li.removeClass('collapsed').addClass('expanded');
                li.children('ul.children').slideDown(100);
                $(this).removeClass('expand').addClass('collapse');
            } else {
                li.removeClass('expanded').addClass('collapsed');
                li.children('ul.children').slideUp(100);
                $(this).removeClass('collapse').addClass('expand');
            }
        });

        // if the clicked element is a child, ensures parent loaded first. if the element is already
        // loaded, then just move the view port appropriately.
        self.$element.on('click', 'a', function(e) {
            e.preventDefault();
            self.loadClickedItem($(e.target));
            var li = $(e.target).parent();
            if (li.hasClass('has-children') && li.hasClass('collapsed')) {
                $(e.target).find('.icon').trigger('click');
            }
        }.bind(self));
    };

    /**
     * Calls the function that will set the view port to the given element for the menu item.
     */
    PatientSidebar.prototype.moveTo = function($item) {
        var elementTypeClass = $item.parents('li:first').data('element-type-class');
        moveToElement($('section[data-element-type-class="' + elementTypeClass + '"]'));
    };

    /**
     * Determines whether clicked item is a child or not, whether it or its parent are currently visible,
     * and thereby what loading actions to call (including calling itself if necessary.
     *
     * @param item
     * @param data
     * @param callback
     */
    PatientSidebar.prototype.loadClickedItem = function($item, data, callback)
    {
        var self = this;
        if (!$item.hasClass('selected')) {
            if ($item.parent().hasClass('child')) {
                // child element, need to ensure parent loaded first.
                var $parent = $item.parents('li:last').find('a:first');
                if (!$parent.hasClass('selected')) {
                    $parent.addClass('selected');
                    // construct a callback to run this method with the original target,
                    // once the parent is loaded
                    var newCallback = function() {
                        self.loadClickedItem($item, data, callback);
                    }.bind($item, data, callback);
                    self.loadElement($parent, {}, newCallback);
                    return;
                }
            }
            // either has no parent or parent is already loaded.
            $item.addClass('selected');
            self.loadElement($item, data, callback);
        } else {
            self.moveTo($item);
            if (callback)
              callback();
        }
    }

    /**
     * Loads a selected element
     *
     * Will load the parent element and then the child element
     * or just the parent element
     *
     */
    PatientSidebar.prototype.loadElement = function(item, data, callback) {
        var self = this;
        var $parentLi = $(item).parents('li:first');
        if (data === undefined)
            data = {};

        if (self.options['event_id'] !== undefined) {
            data['event_id'] = self.options['event_id'];
        }
        addElement($parentLi.clone(true), true, $parentLi.hasClass('child'), undefined, data, callback);
    };

    /**
     * Called when an element is removed from the form to update the menu appropriately.
     */
    PatientSidebar.prototype.removeElememt = function(element) {
        var self = this;
        var elementTypeClass = $(element).parents('section:first').data('element-type-class');

        var $menuLi = self.findMenuItemForElementClass(elementTypeClass);

        if ($menuLi) {
            $menuLi.find('a').removeClass('selected').removeClass('error');
        }
    };

    /**
     * Method to call externally to trigger a load of an element.
     *
     * @param elementTypeClass
     * @param data
     */
    PatientSidebar.prototype.addElementByTypeClass = function(elementTypeClass, data, callback)
    {
        var self = this;
        var $menuLi = self.findMenuItemForElementClass(elementTypeClass);

        if ($menuLi) {
            $href = $menuLi.find('a');
            $href.removeClass('selected').removeClass('error');
            self.loadClickedItem($href, data, callback);
        } else {
            self.error('Cannot find menu entry for given elementTypeClass '+elementTypeClass);
        }

    };

    /**
     * Simple convenience wrapper to grab out the menu entry
     *
     * @param elementTypeClass
     * @returns {*}
     */
    PatientSidebar.prototype.findMenuItemForElementClass = function(elementTypeClass)
    {
        var self = this;

        var $menuLi;
        self.$element.find('li').each(function() {
            if ($(this).data('element-type-class') == elementTypeClass) {
                $menuLi = $(this);
                return;
            }
        });

        return $menuLi;
    };

    /**
     *  Builds the array of open elements on the page
     */
    PatientSidebar.prototype.openElements = function() {
        var self = this;

        self.patient_open_elements = $('.element, .sub-element')
          .map(function() {
              return $(this).data('element-type-class');
          }).get();
    };

    /**
     *  Build the array of elements that have errors using the open elements as a loop
     *
     */
    PatientSidebar.prototype.errorElements = function() {
        var self = this;

        self.patient_error_elements = []
        self.patient_open_elements.forEach(function(element) {
            if ($('a.errorlink[onclick*="' + element + '"]').length) {
                self.patient_error_elements.push(element);
            }
        });
    };

    /**
     *  Build the tree by looping through the JSON
     *
     */
    PatientSidebar.prototype.buildTree = function() {
        var self = this;


        $.each(self.patient_sidebar_array, function () {
            self.$element.append(
              self.buildTreeItem(this)
            );
        });
    };

    /**
     *  Build an item to add to the tree, can be called recusively to add children to a parent.
     *
     */
    PatientSidebar.prototype.buildTreeItem = function(itemData, child) {
        var self = this;
        if (child == undefined)
          child = false;

        var open = $.inArray(itemData.class_name, self.patient_open_elements) !== -1;
        var itemClass;
        var hrefClass = '';
        var span = '';
        if (itemData.children && itemData.children.length) {
            itemClass = 'has-children';
            itemClass += open ? ' expanded' : ' collapsed';
            hrefClass = 'has-icon';
            span = '<span class="icon ' + (open ? 'collapse' : 'expand') + '"></span>';

        } else if (child) {
            itemClass = 'child';
            hrefClass = 'has-icon';
            span = '<span class="icon child"></span>';

        } else {
            itemClass = 'normal';
        }

        if (open)
            hrefClass += ' selected';

        var error = $.inArray(itemData.class_name, self.patient_error_elements) !== -1;
        if (error) {
            hrefClass += ' error';
        }

        var item = $("<li>")
          .data('element-type-class', itemData.class_name)
          .data('element-type-id', itemData.id)
          .data('element-display-order', itemData.display_order)
          .data('element-type-name', itemData.name)
          .addClass(itemClass);


        item.append('<a href="#" class="' + hrefClass + '">' + itemData.name + span + '</a>');

        if (itemData.children && itemData.children.length) {
            var subList = $("<ul>").attr('id',itemData.class_name + '-children').addClass('children');
            $.each(itemData.children, function () {
                subList.append(self.buildTreeItem(this, true).data('container-selector', 'section[data-element-type-id="' + itemData.id + '"]'));
            });

            if (!open) {
                subList.hide();
            }
            item.append(subList);
        }
        return item;
    };

    /**
     *  Convert the JSON into an array
     *
     */
    PatientSidebar.prototype.parseJSON = function() {
        var self = this;
        self.patient_sidebar_array = $.parseJSON(self.options.patient_sidebar_json);
    };

    exports.PatientSidebar = PatientSidebar;

}(OpenEyes.UI));
