
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
        scroll_selector: '#episodes-and-events'
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

        var $scrollElement = self.$element;
        var $newContent = $('<div class="groupings"></div>');

        self.$element = $newContent;

        self.openElements();

        self.errorElements();

        self.parseJSON();

        self.buildTree();
        $scrollElement.append($newContent);


      // find and set up all collapse-groups
      $('.collapse-group').each(function() {
        var group = new CollapseGroup($(this).find('.collapse-group-icon .oe-i'),
          $(this).find('.collapse-group-header'),
          $(this).find('.collapse-group-content'),
          $(this).data('collapse'));
      });

        // if the clicked element is a child, ensures parent loaded first. if the element is already
        // loaded, then just move the view port appropriately.
        self.$element.on('click', '.element', function(e) {
            e.preventDefault();
            self.loadClickedItem($(e.target));
        }.bind(self));
    };

  function CollapseGroup( icon, header, content, initialState ){
    var $icon = icon,
      $header = header,
      $content = content,
      expanded = initialState == 'expanded' ? true : false;

    if(expanded == false) $content.removeClass('hidden').hide();

    $icon.click(function(){
      change();
    });

    $header.click(function(e){
      headerChange(e);
    });

    function headerChange(e){
      PatientSidebar.prototype.loadClickedParent($(e.target).parent());
      if(!expanded){
        e.preventDefault();
        $content.show();
        $icon.toggleClass('minus plus');
        expanded = !expanded;
      }
    }
    function change(){
      if(expanded){
        $content.hide();
      } else {
        $content.show();
      }

      $icon.toggleClass('minus plus');
      expanded = !expanded;
    }
  }
  /**
     * Calls the function that will set the view port to the given element for the menu item.
     */
    PatientSidebar.prototype.moveTo = function($item) {
        var elementTypeClass = $item.parents('li:first').data('element-type-class');
        moveToElement($('section[data-element-type-class="' + elementTypeClass + '"]'));
    };

    /**
     * Get sidebar items where elements are already created , but where the sidebar might not be selected
     * @param $item
     * @returns {Array}
     */
    PatientSidebar.prototype.getSidebarItemsForExistingElements = function($item) {
        let existingItems = [];
        let sidebarChildren = $item.parent().find('ul');
        $.each(sidebarChildren.children(), function (index, item) {
            if ($('section[data-element-type-name="' + $(item).find('a').text() + '"]').length) {
                existingItems.push(item);
            }
        });
        return existingItems;
    };

    /**
     * Determines whether clicked item is a child or not, whether it or its parent are currently visible,
     * and thereby what loading actions to call (including calling itself if necessary.
     *
     * @param item
     * @param data
     * @param callback
     */
    PatientSidebar.prototype.markSidebarItems = function(items){
       items.forEach(function(item){
           $(item).find('a').addClass('selected');
       });
    };

    PatientSidebar.prototype.loadClickedItem = function($item, data, callback)
    {
        var self = this;

        if (!$item.hasClass('selected')) {
            self.markSidebarItems(self.getSidebarItemsForExistingElements($item));

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
                    self.loadElement($ancestor, {}, newCallback);
                    return;
                }
            }
            // either has no parent or parent is already loaded.
            $item.addClass('selected');
            self.loadElement($parent, data, callback);
        } else {
            self.moveTo($item);
            if (callback)
              callback();
        }
    };

    /**
     * Loads a selected element
     *
     * Will load the parent element and then the child element
     * or just the parent element
     *
     */
    PatientSidebar.prototype.loadElement = function(item, data, callback) {
        var self = this;
        var $parentLi = $(item);
        if (data === undefined)
            data = {};

        addElement($parentLi.clone(true), true, !$parentLi.hasClass('has-child'), undefined, data, callback);
    };

    /**
     * Called when an element is removed from the form to update the menu appropriately.
     */
    PatientSidebar.prototype.removeElement = function(element) {
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
     *  Builds the array of open elements on the page
     */
    PatientSidebar.prototype.openElements = function() {
        var self = this;

        self.patient_open_elements = $('.element')
          .map(function() {
              return $(this).data('element-type-class');
          }).get();

        console.log(self.patient_open_elements);
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
    PatientSidebar.prototype.buildTreeItem = function(itemData) {
        var self = this;
        var open = $.inArray(itemData.class_name, self.patient_open_elements) !== -1;
        var itemClass = "collapse-group";
        var hrefClass = 'has-icon';

        if (open)
            hrefClass += ' selected';

        var error = $.inArray(itemData.class_name, self.patient_error_elements) !== -1;
        if (error) {
            hrefClass += ' error';
        }

        var item = $("<div>")
          .data('element-type-class', itemData.class_name)
          .data('element-type-id', itemData.id)
          .data('element-display-order', itemData.display_order)
          .data('element-type-name', itemData.name)
          .attr('data-collapse', 'collapsed')
          .attr('id','side-element-'+itemData.name.replace(/\s+/g,'-'))
          .addClass(itemClass);

      item.append('<a href="#" class="' + hrefClass + '"></a>');
      item.append('<div class="collapse-group-icon"><i class="oe-i pro-theme plus"></i></div> <h3 class="collapse-group-header">'+itemData.name+'</h3>');
        //children
        if (itemData.children && itemData.children.length) {
             var subList = $('<ul>').addClass('oe-element-list collapse-group-content');

        item.append('<div class="collapse-group-icon"><i class="oe-i pro-theme minus"></i></div> <h3 class="collapse-group-header">'+itemData.name+'</h3>');
        //children
        if (itemData.children && itemData.children.length) {
             var subList = $('<ul>').addClass('oe-element-list collapse-group-content');

            $.each(itemData.children, function () {

              var id_name = this.name.replace(/\s+/g,'-');
              var subListItem = $("<li>")
                .data('container-selector','section[data-element-type-id="'+itemData.id+'"]')
                .data('element-type-class', this.class_name)
                .data('element-type-id', this.id)
                .data('element-display-order', this.display_order)
                .data('element-type-name', this.name)
                .attr('id','side-element-'+id_name ).addClass('element');
              var childClass = '';
              if ($.inArray(this.class_name, self.patient_open_elements)!== -1){
                childClass='selected';
              }
              subListItem.append('<a href="#" class= "'+childClass+'" >'+this.name+'</a>');
              subList.append(subListItem);
            });

            if (!open) {
                subList.hide();
            }
            item.append(subList);
            item.addClass('has-children');
            item.addClass('has-children');
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
