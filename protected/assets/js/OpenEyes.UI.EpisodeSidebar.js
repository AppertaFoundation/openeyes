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
     * EpisodeSideBar constructor. The EpisodeSideBar manages the controls of the patient episode side bar when in single
     * episode behaviour, managing the sorting and grouping of the patient events.
     *
     * @param options
     * @constructor
     */
    function EpisodeSidebar(element, options) {
        this.element = $(element);
        this.options = $.extend(true, {}, EpisodeSidebar._defaultOptions, options);
        this.create();
    }

    var groupings = [
        {id: 'none', label: 'Events by date'},
        {id: 'event-year-display', label: 'Events by year'},
        // removed due to similiarity to Year filtering
        //{id: 'event-date-display', label: 'Date'},
        {id: 'event-type', label: 'Events by type'},
        {id: 'subspecialty', label: 'Specialty'}
    ];

    EpisodeSidebar._defaultOptions = {
        switch_firm_text: 'Please switch firm to add an event to this episode',
        user_context: null,
        event_button_selector: '#add-event',
        subspecialty_labels: {},
        event_list_selector: '.events li',
        grouping_picker_class: 'grouping-picker',
        default_sort: 'desc',
        scroll_selector: 'div.oe-scroll-wrapper'
    };

    var sidebarCookie = 'oe-sidebar-state';

    /**
     * Load the previous state of the sidebar from cookie storage
   */
  EpisodeSidebar.prototype.loadState = function () {
    var self = this;
    if (typeof(Storage) !== "undefined") {
      state = sessionStorage.getItem(sidebarCookie);
      if (state) {
        stateObj = JSON.parse(state);
                if (stateObj.sortOrder)
                    self.sortOrder = stateObj.sortOrder;
                if (stateObj.grouping)
                    self.grouping = stateObj.grouping;
            }
        }
    };

    /**
     * Save the current sidebar state to cookie storage
   */
  EpisodeSidebar.prototype.saveState = function () {
    var self = this;
    if (typeof(Storage) !== "undefined") {
      var state = {
        sortOrder: self.sortOrder,
        grouping: self.grouping
            };
            sessionStorage.setItem(sidebarCookie, JSON.stringify(state));
        }
    };

    EpisodeSidebar.prototype.create = function () {
        let self = this;
        let $selected_event = this.element.find(this.options.event_list_selector + '.selected');

        if ($selected_event.length) {
            let li_offset_top = $selected_event[0].offsetTop;
            if (li_offset_top) {
                this.element[0].scrollTop = (this.element[0].scrollHeight - li_offset_top);
            }
        }

    if (self.options.default_sort == 'asc') {
      self.sortOrder = 'asc';
    }
    else {
      self.sortOrder = 'desc';
    }
    self.grouping = {
            id: groupings[0].id
        };

        var $scrollElement = self.element.parents(self.options.scroll_selector + ':first');
        if ($scrollElement.length) {
            // if the scrollbar controller is in place, then when we have changed the content
            // we want to trigger the resize event, as the element list may be longer than the
            // original contents.
            self.sidebarController = $scrollElement.data('sidebar');
        }

        self.lastSort = null;
        self.loadState();

        self.addControls();

        self.updateGrouping();

        self.element.on('click', '.collapse-all', function (e) {
            self.collapseAll();
            e.preventDefault();
        });

        self.element.on('click', '.expand-all', function (e) {
            self.expandAll();
            e.preventDefault();
        });

        self.element.on('click', '.collapse-group-icon i.minus', function (e) {
            self.collapseGrouping($(e.target).parents('.collapse-group'));
            e.preventDefault();
        });

        self.element.on('click', '.collapse-group-icon i.plus', function (e) {
            self.expandGrouping($(e.target).parents('.collapse-group'));
            e.preventDefault();
        });

        function getFirstImageToLoadIndex(index, imageCount) {
            let halfImageCount = imageCount / 2;
            if (halfImageCount >= index) {
                return 1;
            } else {
                return index - halfImageCount;
            }
        }

      self.element.one('mouseenter', '.event-type' , function(){
          let $screenshots = $('.oe-event-quickview .quickview-screenshots');
          let $loader = $('.oe-event-quickview .spinner');
          $screenshots.find('img').each(function () {
                $(this).load(function () {
                    $(this).data('loaded', true);
                    if ($(this).css('display') !== 'none') {
                        $loader.hide();
                    }
                    showCurrentEventImage();
                });
            });
        });

        self.element.on('mouseenter', '.event-type', function (e) {
            var $iconHover = $(e.target);
            var $li = $iconHover.parent().parents('li:first');
            $li.find('.quicklook').show();

            var $screenshots = $('.oe-event-quickview .quickview-screenshots');
            $screenshots.find('img').hide();

            $('.oe-event-quickview #js-quickview-data').text($li.data('event-date-display'));
            $('.oe-event-quickview .event-icon').html($li.data('event-icon'));
            $('.oe-event-quickview').stop().fadeTo(50, 100, function () {
                $(this).show();
            });
            $('.oe-event-quickview').data('current_event', $li.data('event-id'));

            showCurrentEventImage();
        });

        self.element.on('mouseleave', '.event-type', function (e) {
            var $iconHover = $(e.target);
            $iconHover.parents('li:first').find('.quicklook').hide();
            $('.oe-event-quickview').stop().fadeTo(150, 0, function () {
                $(this).hide();
            });
        });


        //Shows the current event image if it's loaded and the quickview is open
        function showCurrentEventImage() {
            //First check the parent element is visible
            let quickview = $('.oe-event-quickview');
            let loader = quickview.find('.spinner');
            let event_id = quickview.data('current_event');
            if (quickview.is(':visible') && event_id) {
                console.log('hey rick the quickview is visible aww jeeze');
                let img = quickview.find('img[data-event-id=' + event_id + ']');
                if (img.data('loaded')){
                    img.show();
                    loader.hide();
                } else {
                    console.log('well we gotta wait morty');
                    loader.show();
                }
            } else {
                console.log('hey rick the quickview isn\'t visible aww jeeze');
            }
        }

        function setEventImageSrc(event_id, url){
            let img = $('img[data-event-id=' + event_id + ']');
            img.attr('src', url);
            img.data('src', url);
        }


        $(document).ready(function () {
            setTimeout(function () {

                let events = [];
                $('.event-type').each(function () {
                    events.push($(this).parents('li:first'));
                });

                var event_ids = [];
                events.forEach(function (event) {
                    event_ids.push(event.data('event-id'));
                });

                let bulkURLFunc = function (response) {
                    let data = JSON.parse(response);
                    //Set the event image source urls for events which are already generated
                    if(data.generated_image_urls){
                        for (let event_id in data.generated_image_urls) {
                            if (data.generated_image_urls.hasOwnProperty(event_id)) {
                                setEventImageSrc(event_id, data.generated_image_urls[event_id]);
                            }
                        }
                    }

                    //Send a parallel request for each of the remaining events
                    // In production this would almost never be more than one but demo data would have no images
                    if (data.remaining_event_ids) {
                        let remaining_events = data.remaining_event_ids;
                        for (let event in remaining_events) {
                            if (remaining_events.hasOwnProperty(event)) {
                                let event_id = remaining_events[event];
                                $.ajax({
                                    type: 'GET',
                                    url: '/eventImage/getImageUrl',
                                    data: {'event_id': event_id},
                                }).success(function(response){
                                    console.log('Aww jeeze rick theres the payload:' + response);
                                    setEventImageSrc(event_id, response);
                                });
                            }
                        }
                    }

                };

                $.ajax({
                    type: 'GET',
                    url: '/eventImage/getImageUrlsBulk',
                    data: {'event_ids': JSON.stringify(event_ids)},
                }).success(bulkURLFunc);
            }, 0);

        });

        // Create hidden quicklook images to prevent the page load from taking too long, while still allowing image caching
        let counter = 1;
        this.element.find(this.options.event_list_selector).each(function () {
            var $container = $('.oe-event-quickview .quickview-screenshots');
            if ($container.find('img[data-event-id="' + $(this).data('event-id') + '"]').length > 0) {
                return;
            }

            var $img = $('<img />', {
                class: 'js-quickview-image',
            style: 'display: none;',
            'data-event-id': $(this).data('event-id'),
            'data-src': $(this).data('event-image-url'),
            'data-index': counter ,
        });

      counter++;
            $img.appendTo($container);
        });
    };

    EpisodeSidebar.prototype.orderEvents = function () {
        var self = this;
        if (self.lastSort == self.sortOrder)
            return;

        var items = this.element.find(this.options.event_list_selector);

        var parent = items.parent();

        function dateSort(b, a) {
            var edA = (new Date($(a).data('event-date'))).getTime();
            var cdA = (new Date($(a).data('created-date'))).getTime();
            var edB = (new Date($(b).data('event-date'))).getTime();
            var cdB = (new Date($(b).data('created-date'))).getTime();
            var ret = null;
            // for some reason am unable to do a chained ternery operator for the comparison, hence the somewhat convoluted
            // if statements to perform the comparison here.
      if (edA === edB) {
        if (cdA === cdB) {
          ret = 0;
        }
        else {
          ret = cdA < cdB ? -1 : 1;
        }
      }
      else {
        ret = edA < edB ? -1 : 1;
      }
      return ret;
        }

        var sorted = items.sort(dateSort);

        if (self.sortOrder == 'asc')
            sorted = sorted.get().reverse();

        self.lastSort = self.sortOrder;

        parent.empty().append(sorted);
    };

    EpisodeSidebar.prototype.addControls = function () {
        var self = this;
        var controls = '';
        controls += self.getGroupingPicker();

        $(controls).insertBefore(self.element.find('.events'));
        $(self.getListControls()).insertBefore(self.element.find('.events'));

        self.element.on('change', '.' + self.options.grouping_picker_class, function (e) {
            self.grouping.id = $(e.target).val();
            self.grouping.state = null;
            self.updateGrouping();
            self.saveState();
        });

        self.element.on('click', '.sorting-order.asc', function (e) {
            e.preventDefault();
            self.sortOrder = 'asc';
            self.updateGrouping();
            self.saveState();
        });

        self.element.on('click', '.sorting-order.desc', function (e) {
            e.preventDefault();
            self.sortOrder = 'desc';
            self.updateGrouping();
            self.saveState();
        });
    }

    EpisodeSidebar.prototype.getGroupingPicker = function () {
        var self = this;
        var select = '<div class="sidebar-grouping">';
        select += '<select name="grouping-picker" class="' + self.options.grouping_picker_class + '">';
        $(groupings).each(function () {
            select += '<option value="' + this.id + '"';
            if (self.grouping && self.grouping.id == this.id)
                select += ' selected';
            select += '>' + this.label + '</option>';
        });
        select += '</select></span>';

        return select;
    };

    EpisodeSidebar.prototype.getListControls = function () {
        var controls = '<div class="list-controls">';
        controls += '<span class="sorting-order asc"><i class="oe-i arrow-up pro-theme"></i></span>';
        controls += '<span class="sorting-order desc"><i class="oe-i arrow-down pro-theme"></i></span>';
        controls += '<div class="right">';
        controls += '<span class="expand-all"><i class="oe-i plus pro-theme"></i></span>';
        controls += '<span class="collapse-all"><i class="oe-i minus pro-theme"></i></span>';
        controls += '</div>';
        controls += '</div>';
        return controls;
    };

    EpisodeSidebar.prototype.resetGrouping = function () {
        this.element.find('.collapse-group').remove();
        this.orderEvents();
        this.element.find(this.options.event_list_selector).parent().show();
    };

    EpisodeSidebar.prototype.updateGrouping = function () {
        var self = this;
        self.resetGrouping();
        if (self.grouping.id == 'none')
            return;

        itemsByGrouping = {};
        groupingVals = [];
        self.element.find(self.options.event_list_selector).each(function () {
      var groupingVal = $(this).data(self.grouping.id);
      if (!groupingVal) {
        console.log('ERROR: missing grouping data attribute ' + self.grouping.id);
      }
      else {
        if (!itemsByGrouping[groupingVal]) {
          itemsByGrouping[groupingVal] = [this];
          groupingVals.push(groupingVal);
        }
        else {
          itemsByGrouping[groupingVal].push(this);
        }
      }
        });

        var groupingElements = '<div class="groupings">';
        $(groupingVals).each(function () {
            var grouping = '<div class="collapse-group" data-grouping-val="' + this + '">' +
                '<div class="collapse-group-icon">' +
                '<i class="oe-i minus pro-theme"></i>' +
                '<i class="oe-i plus pro-theme"></i>' +
                '</div>' +
                '<h3 class="collapse-group-header">' +
                this + ' <span class="count">(' + itemsByGrouping[this].length.toString() + ')</span>' +
                '</h3>' +
                '<ol class="events">';

            $(itemsByGrouping[this]).each(function () {
                grouping += $(this).prop('outerHTML');
            });
            grouping += '</ol></div>';
            groupingElements += grouping;
        });
        groupingElements += '</div>';

        $(groupingElements).insertAfter(self.element.find(this.options.event_list_selector).parent());
        self.element.find(this.options.event_list_selector).parent().hide();
        // TODO: here we should expand or collapse based on current state
        self.processGroupingState();

    };

    EpisodeSidebar.prototype.setGroupingState = function (groupingValue, state) {
        if (this.grouping.state == undefined)
            this.grouping.state = {};
        this.grouping.state[groupingValue] = state;
    };

    EpisodeSidebar.prototype.expandGrouping = function (element, saveState) {
        var self = this;
        if (saveState == undefined)
            saveState = true;

        element.find('ol.events').show();

        element.find('.collapse-group-icon i.plus').hide();
        element.find('.collapse-group-icon i.minus').show();

        element.each(function () {
            self.setGroupingState($(this).data('grouping-val'), 'expand');
        });

        if (saveState)
            this.saveState();
    };

    EpisodeSidebar.prototype.collapseGrouping = function (element, saveState) {
        var self = this;
        if (saveState == undefined)
            saveState = true;

        element.find('ol.events').hide();

        element.find('.collapse-group-icon i.minus').hide();
        element.find('.collapse-group-icon i.plus').show();

        element.each(function () {
            self.setGroupingState($(this).data('grouping-val'), 'collapse');
        });

        if (saveState)
            this.saveState();
    };

    EpisodeSidebar.prototype.expandAll = function () {
        this.expandGrouping(this.element.find('.collapse-group'), false);
        this.saveState();
    };

    EpisodeSidebar.prototype.collapseAll = function () {
        this.collapseGrouping(this.element.find('.collapse-group'), false);
        this.saveState();
    };
    //TODO: loading is not working, need to verify where we're at!!
    EpisodeSidebar.prototype.processGroupingState = function () {
    var self = this;
    if (self.grouping.state == undefined) {
      self.expandAll();
    }
    else {
      self.element.find('.collapse-group').each(function () {
        if (self.grouping.state[$(this).data('grouping-val')] == 'collapse') {
          self.collapseGrouping($(this), false);
        }
        else {
          self.expandGrouping($(this), false);
        }
      });
        }
    };

    exports.EpisodeSidebar = EpisodeSidebar;

}(OpenEyes.UI));