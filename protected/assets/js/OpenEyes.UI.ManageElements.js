/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

(function (exports) {

    const $elementPopup = $('<div />', {class: 'all-elements'});

    /**
     * ManageElements constructor
     */
    function ManageElements(element, sidebar, options) {
        this.$element = $(element);
        this.$sidebar = $(sidebar);
        this.$element.data('episodes-and-events', this);
        this.options = $.extend(true, {}, ManageElements._defaultOptions, options);
        this.isOpened = false;
        this.create();
    }

    ManageElements._defaultOptions = {
        manage_elements_json: {},
        element_container_selector: '.js-active-elements',
        tree_id: '',
        manage_element_selector: 'manage-elements-nav'
    };

    /**
     * Creates manage elements popup for the event
     */
    ManageElements.prototype.create = function() {
        let self = this;

        //Create the element selector content
        const $navPopup = $('<nav />', {class: 'oe-element-selector', id: self.options.manage_element_selector});
        let closeButton = $('<div class="close-icon-btn"><button class="blue hint cols-full">Select elements to add or remove from examination - Close when done &nbsp;<i class="oe-i remove-circle pro-theme medium-icon"></i></button></div>');

        $navPopup.append($elementPopup);
        $navPopup.append(closeButton);

        $navPopup.insertAfter(self.$sidebar).hide();

        //Examination viewport where the elements are present
        self.$elementContainer = $(document).find(self.options.element_container_selector);

        self.$elementContainer.on('click', '.js-remove-element', function(e) {
            self.removeElement(e.target);
        });

        self.$sidebar.on('click', '.element', function(e) {
            self.updatePopupItem($(e.target));
        });

        self.getRequiredElements();

        self.getOpenElements();

        self.parseJSON();

        self.buildTree();

        closeButton.click(toggleView);
        self.$element.click(toggleView);
        self.$elementContainer.click(setOffView);

        function toggleView(){
            let $manageElement = self.$element;
            if(!$manageElement.isOpened) {
                $navPopup.show();
                $manageElement.isOpened = true;
                $manageElement.addClass('selected');
            } else {
                $navPopup.hide();
                $manageElement.isOpened = false;
                $manageElement.removeClass('selected');
            }
        }

        function setOffView(){
            let $manageElement = self.$element;
            $navPopup.hide();
            $manageElement.isOpened = false;
            $manageElement.removeClass('selected');
        }

        $navPopup.on('click', '.element-list li', function(e) {
            self.addSelectedElement($(e.target));
        }.bind(self));

        $('li#manage-elements-Medication-Management').data('validation-function', medicationManagementValidationFunction);
    };

    /**
     * Builds the array of elements that are mandatory and cannot be removed
     */
    ManageElements.prototype.getRequiredElements = function() {
        let self = this;

        self.requiredElements = [];

        const containerElements = $('section');

        containerElements.each(function(element) {
            if ($(containerElements[element]).children(".element-actions").find("span").hasClass("disabled")) {
                self.requiredElements.push($(containerElements[element]).data('elementTypeClass'));
            }
        });
    };

    /**
     * Builds the array of elements that are open on the page
     */
    ManageElements.prototype.getOpenElements = function() {
        let self = this;

        self.openElements = [];

        let elementChildren = $(self.$elementContainer.children());

        elementChildren.each(function(item) {
            self.openElements.push($(elementChildren[item]).data('elementTypeClass'));
        });

    };

    /**
     * Add or remove the selected element to the form
     */
    ManageElements.prototype.addSelectedElement = function($item) {
        let elementValidationFunction = $item.data('validation-function');
        let loadItem = typeof elementValidationFunction !== "function" || elementValidationFunction();

        if (loadItem) {
            if(!$item.hasClass('added')) {
                this.addElementItem($item);
            } else {
                this.removeElementItem($item);
            }
        }
    };

    /**
     * Add the element to the form after selecting the element
     */
    ManageElements.prototype.addElementItem = function($item, data, callback) {
        if(data === undefined)
            data = {};
        if(!$item.hasClass('added')) {
            this.showSelectedElements(this.getPopupForExistingElements($item));
            if(typeof callback === "function") {
                callback();
            }
            addElement($item.clone(true), true, undefined, data, callback);
            $item.addClass('added');
        }
    };

    /**
     * Get the list of elements that are added in the form but not added to the popup
     */
    ManageElements.prototype.getPopupForExistingElements = function($item) {
        let existingItems = [];
        if($('section[data-element-type-name="' + $item.text() + '"]').length) {
            existingItems.push($item);
        }
        return existingItems;
    };

    /**
     * Highlight the elements that have been selected
     */
    ManageElements.prototype.showSelectedElements = function (items) {
        items.forEach(function(item){
            $(item).find("li").addClass("added");
        });
    };

    /**
     * Update the menu item when element is added from the sidebar
     */
    ManageElements.prototype.updatePopupItem = function ($item) {
        let menuElement = this.getSidebarItemName($item);
        if(!menuElement.hasClass('mandatory')) {
            menuElement.addClass('added');
        }
    };

    /**
     * Get the name of the sidebar item
     */
    ManageElements.prototype.getSidebarItemName = function ($item) {
        return $elementPopup.find("li").filter(function() {
            return $(this).text() == $item.text();
        });
    }

    /**
     * Remove the element from the form it has been added from the popup
     */
    ManageElements.prototype.removeElementItem = function($item) {
        let $element = $('section[data-element-type-name="' + $item.text() + '"]');
        if($element.children("input").val() == '1') {
            let dialog = new OpenEyes.UI.Dialog.Confirm({
                content: "Are you sure that you wish to close the " +
                    $item.text() +
                    " element? All data in this element will be lost"
            });
            dialog.on('ok', function () {
                // removeElement function from nested_element file
                removeElement($element);
                $item.removeClass("added");
            }.bind(this));
            dialog.open();
        }
        // Only remove the element if it is on the screen
        // Prevents from creating multiple elements of same field
        else if($element.length === 1) {
            // removeElement function from nested_element file
            removeElement($element);
            $item.removeClass('added');
        }
    };

    /**
     * Remove the selected element from the popup when the element is removed from the form
     */
    ManageElements.prototype.removeElement = function($item) {
        let $elementTypeClass = $($item).parents("section").data('elementTypeClass');
        let element = this.getElementTypeClass($elementTypeClass);
        element.removeClass('added');
    }

    /**
     * Get the class type for the element
     */
    ManageElements.prototype.getElementTypeClass = function($elementTypeClass) {
        return $elementPopup.find('li').filter(function() {
            return $(this).data('elementTypeClass') == $elementTypeClass;
        });
    };

    /**
     * Builds the tree to be displayed on the popup from JSON
     *
     */
    ManageElements.prototype.buildTree = function() {
        let self = this;

        $.each(self.manage_elements_array, function() {
            $elementPopup.append(self.buildTreeItem(this));
        });
    };

    /**
     * Build an item to add to the tree
     */
    ManageElements.prototype.buildTreeItem = function(itemData) {
        let item;

        let itemClass = 'selector-group outline';

        item = $("<div>")
            .data('element-type-class', itemData.class_name)
            .data('element-type-id', itemData.id)
            .data('element-display-order', itemData.display_order)
            .data('element-type-name', itemData.name)
            .addClass(itemClass);

        if(!itemData.children || itemData.children.length === 0) {
            item.append(this.buildTreeChildList([itemData]));
        } else {

            item.append('<h3>' + itemData.name + '</h3>');

            let subList = this.buildTreeChildList(itemData.children);

            item.append(subList);
        }

        return item;
    };

    /**
     * Builds the child of a tree item and returns it
     *
     */
    ManageElements.prototype.buildTreeChildList = function (childItems) {
        let self = this;
        let sublist = $("<ul>").addClass('element-list');

        $.each(childItems, function() {
            let id = this.name.replace(/\s/g, '-');
            let subListItem = $("<li id=manage-elements-"+ id +">"+this.name+"</li>")
                .data('element-type-class', this.class_name)
                .data('element-display-order', this.display_order)
                .data('element-type-name', this.name)
                .data('element-type-id', this.id);

            if($.inArray(this.class_name, self.requiredElements)!== -1){
                subListItem.addClass('mandatory');
            }

            if($.inArray(this.class_name, self.openElements)!== -1){
                if(!subListItem.hasClass('mandatory'))
                    subListItem.addClass('added');
            }

            sublist.append(subListItem);
        });

        return sublist;
    };

    /**
     * Convert the JSON into an array
     * 
     */
    ManageElements.prototype.parseJSON = function() {
        this.manage_elements_array = $.parseJSON(this.options.manage_elements_json);
    };

    exports.ManageElements = ManageElements;

}(this.OpenEyes.UI));
