(function (exports) {

    let NavBtnPopUp = exports;

    function HotList(id, $btn, $content, options) {
        // The date to restrict he closed list to. Default to today
        this.selected_date = new Date();
        this.confirm_redirect = true;
        this.patient_search_form = $("#hotlist-search-form");
        this.patient_search_button = $("#js-hotlist-find-patient");
        this.patient_search_input = $('#hotlist-search-text-field');
        NavBtnPopUp.call(this, id, $btn, $content, options);
        this.create();
    }

    HotList.prototype = Object.create(NavBtnPopUp.prototype);
    HotList.prototype.constructor = HotList;

    HotList.prototype.create = function () {
        var hotlist = this;
        autosize($('.activity-list').find('textarea'));

        // activity datepicker using pickmeup.
        // CSS controls it's positioning
        var $pmuWrap = $('#js-pickmeup-datepicker').hide();
        var pmu = pickmeup('#js-pickmeup-datepicker', {
            format: 'a d b Y',
            flat: true,
            position: 'left'
        });

        // vanilla:
        var activityDatePicker = document.getElementById("js-pickmeup-datepicker");

        // When the pickmeup date picker is changed
        activityDatePicker.addEventListener('pickmeup-change', function (e) {
            $pmuWrap.hide();
            hotlist.setSelectedDate(e.detail.date, e.detail.formatted_date);
            hotlist.updateClosedList();
        });


        // When the date picker is clicked
        $('#js-hotlist-closed-select').click(function () {
            $pmuWrap.toggle();
            return false;
        });

        // Hide the date picker if anywhere else on the screen is clicked
        $('body').on('click', function (e) {
            if (!$(e.target).closest('#js-pickmeup-datepicker').length) {
                $('#js-pickmeup-datepicker').hide();
            }
        });

        // When the "Today" date link is clicked
        $('#js-hotlist-closed-today').click(function () {
            pmu.set_date(new Date());
            hotlist.setSelectedDate(new Date(), 'Today');
            hotlist.updateClosedList();
        });

        // When a patient record is clicked
        $('.activity-list').on('click', '.js-hotlist-open-patient, .js-hotlist-closed-patient, .js-hotlist-draft-event', function () {
            hotlist.leavePageAlert();

            if (hotlist.confirm_redirect) {
                window.location.href = $(this).data('patient-href');
            }
        });

        hotlist.patient_search_button.off('click').on('click', function(e) {
            e.preventDefault();
            hotlist.leavePageAlert();

            if (hotlist.confirm_redirect) {
                hotlist.patient_search_form.trigger('submit');
            }
        });

        hotlist.patient_search_input.off('keyup').on('keyup', function(e) {
            if (e.which === $.ui.keyCode.ENTER) {
                hotlist.leavePageAlert();
                if (hotlist.confirm_redirect) {
                    hotlist.patient_search_form.trigger('submit');
                }
            }
        });

        // When the open link in a closed item is clicked
        $('.activity-list.closed').delegate('.js-open-hotlist-item', 'click', function () {
            var itemId = $(this).closest('.js-hotlist-closed-patient').data('id');
            $.ajax({
                type: 'GET',
                url: '/UserHotlistItem/openHotlistItem',
                data: {hotlist_item_id: itemId},
                success: function () {
                    hotlist.updateOpenList();
                }
            });
            hotlist.removeItem(itemId);
            return false;
        });

        // When the close link in an open item is clicked
        $('.activity-list.open').delegate('.js-close-hotlist-item', 'click', function () {

            var itemId = $(this).closest('.js-hotlist-open-patient').data('id');
            $.ajax({
                type: 'GET',
                url: '/UserHotlistItem/closeHotlistItem',
                data: {hotlist_item_id: itemId},
                success: function () {
                    hotlist.updateClosedList();
                }
            });
            hotlist.removeItem(itemId);
            return false;
        });

        var commentUpdateTimeout;
        // When the enter key is pressed when editing a comment
        $('.activity-list').delegate('.js-hotlist-comment textarea', 'keyup', function () {
            var comment = $(this).val();
            var itemId = $(this).closest('.js-hotlist-comment').data('id');
            clearTimeout(commentUpdateTimeout);
            commentUpdateTimeout = setTimeout(function () {
                hotlist.updateComment(itemId, comment);
            }, 500);
        });

        // When the comment button in any item is clicked
        $('.activity-list').delegate('.js-add-hotlist-comment', 'click', function () {
            var hotlistItem = $(this).closest('.js-hotlist-open-patient, .js-hotlist-closed-patient');
            var itemId = hotlistItem.data('id');
            var commentRow = hotlistItem.siblings('.js-hotlist-comment[data-id="' + itemId + '"]');
            if (commentRow.css('display') !== 'none') {
                hotlist.updateComment(itemId, commentRow.find('textarea').val());
                commentRow.hide();
            } else {
                commentRow.show();
                commentRow.find('textarea').focus();
            }
            return false;
        });

        $('.js-hotlist-event-drafts .overview').on('click', function() {
            const toggler = $(this);
            const list = $('.js-hotlist-event-drafts .hidden');

            if (toggler.hasClass('expand')) {
                toggler.removeClass('expand').addClass('collapse');

                list.show();
            } else {
                toggler.removeClass('collapse').addClass('expand');

                list.hide();
            }
        });

        $(".js-hotlist-panel-wrapper").on({
            mouseenter: function () {
                hotlist.commentsQuickLook(true, $(this));
            },
            mouseleave: function () {
                hotlist.commentsQuickLook(false);
            }
        }, '.js-patient-comments');
    };

    HotList.prototype.commentsQuickLook = function (show, $icon) {
        let $quick = $('#hotlist-quicklook');
        let text = $icon ? $icon.closest('tr').next().find('textarea').val() : null;
        if (!show) {
            $quick.hide();
            return;
        }
        if (text !== "") {
            $quick.text(text).show().css('top',($icon.position().top + 19));
        }
    };

    HotList.prototype.setSelectedDate = function (date, display_date) {
        this.selected_date = date;
        if (this.selected_date.toDateString() === (new Date).toDateString()) {
            $('#js-pickmeup-closed-date').text('Today');
        } else {
            $('#js-pickmeup-closed-date').text(display_date);
        }
    };

    HotList.prototype.updateClosedList = function () {
        var hotlist = this;
        $.ajax({
            type: 'GET',
            url: '/UserHotlistItem/renderHotlistItems',
            data: {
                is_open: 0,
                date: this.selected_date.getFullYear() + '-' + (this.selected_date.getMonth() + 1) + '-' + this.selected_date.getDate()
            },
            success: function (response) {
                $('table.activity-list.closed').find('tbody').html(response);
                hotlist.updateListCounters();
                autosize($('.activity-list').find('textarea'));
            }
        });
    };

    HotList.prototype.updateOpenList = function () {
        var hotlist = this;
        $.ajax({
            type: 'GET',
            url: '/UserHotlistItem/renderHotlistItems',
            data: {is_open: 1, date: null},
            success: function (response) {
                $('table.activity-list.open').find('tbody').html(response);
                hotlist.updateListCounters();
                autosize($('.activity-list').find('textarea'));
            }
        });
    };

    HotList.prototype.updateListCounters = function () {
        $('.patients-open .count').text($('.activity-list.open .js-hotlist-open-patient').length);
        $('.patients-closed .count').text($('.activity-list.closed .js-hotlist-closed-patient').length);
    };

    HotList.prototype.removeItem = function (itemId) {
        $('.activity-list tr[data-id="' + itemId + '"]').remove();
        $('body').find(".oe-tooltip").remove();
    };

    HotList.prototype.updateComment = function (itemId, userComment) {
        var hotlistItem = $('.activity-list tr[data-id="' + itemId + '"]');
        var shortComment = userComment.substr(0, 30) + (userComment.length > 30 ? '...' : '');
        var readonlyComment = hotlistItem.find('.js-hotlist-comment-readonly');
        readonlyComment.text(shortComment);
        readonlyComment.data('tooltip-content', userComment);
        var commentIcon = hotlistItem.find('i.js-add-hotlist-comment');
        commentIcon.removeClass('comments comments-added active');
        commentIcon.addClass(userComment.length > 0 ? 'comments-added active' : 'comments');
        $.ajax({
            type: 'GET',
            url: '/UserHotlistItem/updateUserComment',
            data: {hotlist_item_id: itemId, comment: userComment}
        });
    };

    HotList.prototype.leavePageAlert = function() {
        if (!window.is_editing_event) {
            this.confirm_redirect = true;
            return;
        }

        window.onbeforeunload = null;
        this.confirm_redirect = window.confirm("Changes you made may not be saved. Are you sure that you wish to leave the page?");
    }

    exports.HotList = HotList;

}(OpenEyes.UI.NavBtnPopup));
