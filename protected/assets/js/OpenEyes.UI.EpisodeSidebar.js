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

(function (exports) {
    /**
     * EpisodeSideBar constructor. The EpisodeSideBar manages the controls of the patient episode side bar when in single
     * episode behaviour, managing the sorting and grouping of the patient events.
     *
     * @param element
     * @param options
     * @constructor
     */
    function EpisodeSidebar(element, options) {
        this.element = $(element);
        this.sendImageUrlAjaxRequest = true;
        this.options = $.extend(true, {}, EpisodeSidebar._defaultOptions, options);
        this.create();
    }

    const groupings = [
        {id: 'none', label: 'Events by date'},
        {id: 'institution', label: 'Events by institution'},
        {id: 'event-year-display', label: 'Events by year'},
        // removed due to similiarity to Year filtering
        //{id: 'event-date-display', label: 'Date'},
        {id: 'event-type', label: 'Events by type'},
        {id: 'subspecialty', label: 'Specialty'},
        {id: 'deleted', label: 'Deleted events'}
    ];

    let pinned_quickview_id = null;

    EpisodeSidebar._defaultOptions = {
        switch_firm_text: 'Please switch firm to add an event to this episode',
        user_context: null,
        event_button_selector: '#add-event',
        subspecialty_labels: {},
        event_list_selector: '.events li.complete',
        deleted_event_list_selector: '.events li.complete.deleted',
        draft_list_selector: '.events li.draft',
        deleted_draft_list_selector: '.events li.draft.deleted',
        grouping_picker_class: 'grouping-picker',
        default_sort: 'desc',
        scroll_selector: 'div.oe-scroll-wrapper',
        close_quicview_selector: '#close-quickview',
        deleted_event_category: false
    };

    const sidebarCookie = 'oe-sidebar-state';

    /**
     * Load the previous state of the sidebar from cookie storage
     */
    EpisodeSidebar.prototype.loadState = function () {
        const self = this;
        if (typeof (Storage) !== "undefined") {
            let state = sessionStorage.getItem(sidebarCookie);
            if (state) {
                let stateObj = JSON.parse(state);
                if (stateObj.sortOrder) {
                    self.sortOrder = stateObj.sortOrder;
                }
                if (stateObj.grouping) {
                    self.grouping = stateObj.grouping;
                }
            }
        }
    };

    /**
     * Save the current sidebar state to cookie storage
     */
    EpisodeSidebar.prototype.saveState = function () {
        const self = this;
        if (typeof (Storage) !== "undefined") {
            const state = {
                sortOrder: self.sortOrder,
                grouping: self.grouping
            };
            sessionStorage.setItem(sidebarCookie, JSON.stringify(state));
        }
    };

    EpisodeSidebar.prototype.create = function () {
        let self = this;

        if (self.options.default_sort === 'asc') {
            self.sortOrder = 'asc';
        } else {
            self.sortOrder = 'desc';
        }
        self.grouping = {
            id: groupings[0].id
        };

        const $scrollElement = self.element.parents(self.options.scroll_selector + ':first');
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

        let $selected_event = this.element.find(this.options.event_list_selector + '.selected');
        let li_height = $selected_event.height();
        let min_viewport_height = $(window).height() - (li_height * 3);

        if ($selected_event.length) {
            let li_offset_top = $selected_event[0].offsetTop;
            let height_offset = $('header.oe-header').height() + $('nav.sidebar-header').height();

            if (li_offset_top && li_offset_top >= min_viewport_height) {
                this.element[0].scrollTop = (li_offset_top - (height_offset + (li_height * 10)));
            }
        }

        self.element.on('click', '.collapse-group-icon i.plus', function (e) {
            self.expandGrouping($(e.target).parents('.collapse-group'));
            e.preventDefault();
        });

        function setBoxDetails($li) {
            const $eventQuickview =  $('.oe-event-quickview');
            $('.oe-event-quickview #js-quickview-data').text($li.data('event-date-display'));
            $('.oe-event-quickview .event-icon').html($li.data('event-icon'));
            $('.oe-event-quickview .title').html($li.data('event-type') + " - " + $li.data('event-date-display'));
            $eventQuickview.stop().fadeTo(50, 100, function () {
                $(this).show();
            });
            $eventQuickview.data('current_event', $li.data('event-id'));
        }

        async function loadImage($li, type = '') {
            // Don't show image for draft events
            if ($li.hasClass('draft')) {
                return;
            }

            const $quickview =  $('.oe-event-quickview');
            let event_id = $li.data('event-id');
            const $screenshots = $('.oe-event-quickview .quick-view-content');
            $screenshots.find('img').hide();
            $quickview.find('.spinner').show();
            setBoxDetails($li);
            let load_timeout = 0;
            let imgs = $quickview.find('img[data-event-id=' + event_id + ']');

            // if no imgs, generate empty img tags according info from server
            if (!imgs.length) {
                load_timeout = 500;
                const response = await fetch(`/eventImage/getImageInfo?event_id=${event_id}`);
                const json = await response.json();
                var isBilateral = false;
                var eye_ids = {"right": 2, "left": 1};
                let $imgs = [];

                if (json.hasOwnProperty("right") || json.hasOwnProperty("left")) {
                    isBilateral = true;
                    for (const side in eye_ids) {
                        if (json.hasOwnProperty(side)) {
                            $imgs = $imgs.concat(generateImgTags(event_id, json[side].page_count, eye_ids[side]));
                        }
                    }
                } else {
                    $imgs = generateImgTags(event_id, json.page_count);
                }

                const $container = $quickview.find('.quick-view-content');
                $imgs.forEach(($img) => {
                    $img.appendTo($container);
                });
            }

            // at this point we should have generated image tags, let's show images
            setTimeout(function() {
                // let's select again all the images (in case if we just generated them)
                let imgs = $quickview.find('img[data-event-id=' + event_id + ']');
                if (type === 'first') {
                    if (isBilateral) {
                        imgs = [];
                        for (const side in eye_ids) {
                            const eye_id = eye_ids[side];
                            //get the image with page 0
                            let img = $quickview.find('img[data-event-id=' + event_id + '][data-src$="page=0&eye_id=' + eye_id + '"]');
                            if (img.length === 0) { //if not found let's check that there is a single image for that side
                                img = $quickview.find('img[data-event-id=' + event_id + '][data-src$="eye_id=' + eye_id + '"]');
                            }
                            if (img) {
                                imgs.push(img);
                            }
                        }
                    } else {
                        imgs = [imgs[0]];
                    }
                }
                $.each(imgs, (index, img) => {
                    if ($(img).attr('src') === undefined) {
                        fetch($(img).data('src')).then(response => response.text())
                            .then(url => {
                                $(img).attr('src', url);
                                $(img).data('loaded', true);

                                // to instantly show new images, like page 2 or 3 ...
                                showCurrentEventImage();
                            });
                    }
                });
                showCurrentEventImage();
            }, load_timeout);

        }

        self.element.on('click', '.event-type', (e) => {
            e.preventDefault();
            const $li = $(e.target).closest('li');

            if (pinned_quickview_id === null) {
                pinned_quickview_id = e.target.parentNode.dataset.id;
                loadImage($li);
                $(this.options.close_quicview_selector).show();
            } else if (pinned_quickview_id !== e.target.parentNode.dataset.id) {
                pinned_quickview_id = e.target.parentNode.dataset.id;
                loadImage($li);
                $(this.options.close_quicview_selector).show();
            } else if (pinned_quickview_id === e.target.parentNode.dataset.id) {
                pinned_quickview_id = null;
                $(this.options.close_quicview_selector).hide();
            }
        });

        $(this.options.close_quicview_selector).on('click', () => {
            pinned_quickview_id = null;
            self.element.find('.event-type').trigger('mouseleave');
            $(this.options.close_quicview_selector).hide();
        });

        self.element.on('mouseenter', '.event-type', function (e) {
            const $iconHover = $(e.target);
            const $li = $iconHover.closest('li');
            $li.find('.quicklook').show();

            if (pinned_quickview_id !== null) {
                return true;
            }

            loadImage($li, 'first');
        });

        self.element.on('mouseleave', '.event-type', function (e) {
            const $iconHover = $(e.target);
            $iconHover.closest('li').find('.quicklook').hide();

            if (pinned_quickview_id !== null) {
                return true;
            }

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
                loader.show();
                let img = quickview.find('img[data-event-id=' + event_id + '][src]');
                if (img.data('loaded')) {
                    img.show();
                    loader.hide();
                } else {
                    loader.show();
                }
            }
        }

        function generateImgTags(event_id, page_count, eye_id = null) {
            let imgs = [];

            for (let i = 0; i < page_count; i++) {
                const url = new URL(`/eventImage/getImageUrl?event_id=${event_id}`, window.location.origin);
                if (page_count > 1) {
                    url.searchParams.set('page', i);
                }
                if (eye_id) {
                    url.searchParams.set('eye_id', eye_id);
                }

                let img = document.querySelector(`img[data-page-num="${i}"][data-src="${url.href}"]`);

                if (!img) {
                    img = $('<img />', {
                        class: 'js-quickview-image',
                        style: 'display: none;' + (page_count > 0 ? 'padding-bottom: 20px' : ''),
                        'data-event-id': event_id,
                        'data-src': url.href,
                        'data-index': i,
                        'data-page-num': i
                    });
                    imgs.push(img);
                }
            }
            return imgs;
        }

        // Create hidden quicklook images to prevent the page load from taking too long, while still allowing image caching
        this.element.find(this.options.event_list_selector).each(function (index) {
            const $container = $('.oe-event-quickview .quick-view-content');
            if ($container.find('img[data-event-id="' + $(this).data('event-id') + '"]').length > 0) {
                return;
            }

            const count = $(this).data('event-image-page-count');
            let steps = 0;
            if (count && Number(count)) {
                steps = parseInt($(this).data('event-image-page-count'))-1;
            }
            const $imgs = generateImgTags($(this).data('event-id'), count, index);

            $imgs.forEach(($img) => {
                $img.appendTo($container);
            });
        });
    };

    EpisodeSidebar.prototype.orderEvents = function () {
        const self = this;
        if (self.lastSort === self.sortOrder) {
            return;
        }

        const items = this.element.find(this.options.event_list_selector);
        const drafts = this.element.find(this.options.draft_list_selector);

        const parent = items.parent();

        function dateSort(b, a) {
            const edA = (new Date($(a).data('event-date'))).getTime();
            const cdA = (new Date($(a).data('created-date'))).getTime();
            const edB = (new Date($(b).data('event-date'))).getTime();
            const cdB = (new Date($(b).data('created-date'))).getTime();
            let ret;
            // for some reason am unable to do a chained ternery operator for the comparison, hence the somewhat convoluted
            // if statements to perform the comparison here.
            if (edA === edB) {
                if (cdA === cdB) {
                    ret = 0;
                } else {
                    ret = cdA < cdB ? -1 : 1;
                }
            } else {
                ret = edA < edB ? -1 : 1;
            }
            return ret;
        }

        let sorted = items.sort(dateSort);

        if (self.sortOrder === 'asc')
            sorted = sorted.get().reverse();

        self.lastSort = self.sortOrder;

        parent.empty().append(drafts).append(sorted);
    };

    EpisodeSidebar.prototype.addControls = function () {
        const self = this;

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
    };

    EpisodeSidebar.prototype.getGroupingLabel = function (id) {
        return groupings.filter((grouping) => { return grouping.id === id; })[0].label;
    }

    EpisodeSidebar.prototype.resetGrouping = function () {
        this.element.find('.groupings').remove();
        this.orderEvents();
        this.element.find(this.options.event_list_selector).parent().show();
        // in case the active events were hidden by clicking the deleted events group
        this.element.find(this.options.event_list_selector).show();
        // if the setting is on, hide the deleted events (will be shown in categories when extracted)
        if(this.options.deleted_event_category){
            this.element.find(this.options.deleted_event_list_selector).hide();
        }
    };
    EpisodeSidebar.prototype.showDeletedEvents = function(){
        this.element.find(this.options.event_list_selector).hide();
        this.element.find(this.options.deleted_event_list_selector).show();
    };
    EpisodeSidebar.prototype.updateGrouping = function () {
        const self = this;
        self.resetGrouping();
        const deleteEventsLabel = self.getGroupingLabel('deleted');

        let itemsByGrouping = {};
        let groupingVals = [];
        self.element.find(self.options.event_list_selector).each(function () {
            let groupingVal = self.options.deleted_event_category && $(this).hasClass('deleted') ? deleteEventsLabel : $(this).data(self.grouping.id);
            if (!groupingVal) {
                if (self.grouping.id === 'none') {
                    groupingVal = self.getGroupingLabel('none');
                } else {
                    console.log('ERROR: missing grouping data attribute ' + self.grouping.id);
                }
            } else {
                if (!itemsByGrouping[groupingVal]) {
                    itemsByGrouping[groupingVal] = [this];
                    groupingVals.push(groupingVal);
                } else {
                    itemsByGrouping[groupingVal].push(this);
                }
            }
        });

        let groupingElements = '<div class="groupings">';

        groupingElements += groupingVals
            .filter(function (groupingVal) {
                return groupingVal !== deleteEventsLabel;
            })
            .map(function (groupingVal) {
                return self.buildGrouping(groupingVal, itemsByGrouping[groupingVal]);
            })
            .join('');

        if (groupingVals.includes(deleteEventsLabel)) {
            groupingElements += self.buildGrouping(deleteEventsLabel, itemsByGrouping[deleteEventsLabel]);
        }

        groupingElements += '</div>';

        $(groupingElements).insertAfter(self.element.find(this.options.event_list_selector).parent());
        if (self.grouping.id === 'none') {
            return;
        }
        self.element.find(this.options.event_list_selector).parent().hide();
        $(self.element.find('select')).val(self.grouping.id);
        self.processGroupingState();

    };

    EpisodeSidebar.prototype.buildGrouping = function (groupingName, items) {
        let grouping = '<div class="collapse-group" data-grouping-val="' + groupingName + '">' +
            '<div class="collapse-group-icon">' +
            '<i class="oe-i minus pro-theme"></i>' +
            '<i class="oe-i plus pro-theme"></i>' +
            '</div>' +
            '<h3 class="collapse-group-header">' +
            groupingName + ' <span class="count">(' + items.length.toString() + ')</span>' +
            '</h3>' +
            '<ol class="events">';

        $(items).each(function () {
            const item = $(this).prop('outerHTML');
            grouping += $(item).prop('style', 'display: block;').prop('outerHTML');
        });
        grouping += '</ol></div>';

        return grouping;
    };

    EpisodeSidebar.prototype.setGroupingState = function (groupingValue, state) {
        if ((this.grouping.state === undefined) || !this.grouping.state)
            this.grouping.state = {};

        if (this.grouping.state === null)
            this.grouping.state = {};

        this.grouping.state[groupingValue] = state;
    };

    EpisodeSidebar.prototype.expandGrouping = function (element, saveState) {
        const self = this;
        if (saveState === undefined)
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
        const self = this;
        if (saveState === undefined)
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
        const self = this;
        if (self.grouping.state === undefined || self.grouping.state === null) {
            self.expandAll();
        } else {
            self.element.find('.collapse-group').each(function () {
                if (!self.grouping.state) {
                    self.expandGrouping($(this), false);
                } else {
                    if (self.grouping.state[$(this).data('grouping-val')] === 'collapse') {
                        self.collapseGrouping($(this), false);
                    } else {
                        self.expandGrouping($(this), false);
                    }
                }
            });
        }
    };

    exports.EpisodeSidebar = EpisodeSidebar;

}(OpenEyes.UI));
