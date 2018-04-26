(function (exports) {

  function HotList(activity) {
    this.create(activity);
  }

  HotList.prototype.create = function (activity) {
    var hotlist = this;

    if ($('#js-nav-activity-btn').length == 0) {
      return;
    }

    // The date to restrict he closed list to. Default to today
    this.selected_date = new Date;

    // Fix Activity Panel if design allows it to be fixable!
    if ($('#js-nav-activity-btn').data('fixable') == true) {
      checkBrowserSize();

      $(window).resize(function () {
        checkBrowserSize();
      });

      function checkBrowserSize() {
        if ($(window).width() > 1800) { // min width for fixing Activity Panel (allows some resizing)
          activity.fixed(true);
        } else {
          activity.fixed(false);
        }
      }
    }

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
    $('#js-activity-closed-select').click(function () {
      $pmuWrap.toggle();
    });

    // When the "Today" date link is clicked
    $('#js-activity-closed-today').click(function () {
      pmu.set_date(new Date);
      hotlist.setSelectedDate(new Date, 'Today');
    });

    // When a patient record is clicked
    $('.activity-list').delegate('.js-activity-open-patient, .js-activity-closed-patient', 'click', function () {
      window.location.href = $(this).data('eventHref');
    });

    // When the close link in an open item is clicked
    $('.activity-list.closed').delegate('.js-open-hotlist-item', 'click', function () {

      var itemId = $(this).closest('.js-activity-closed-patient').data('id');

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

    // WHen the open link in a closed item is clicked
    $('.activity-list.open').delegate('.js-close-hotlist-item', 'click', function () {

      var itemId = $(this).closest('.js-activity-open-patient').data('id');

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

    // When the enter key is pressed when editing a comment
    $('.activity-list').delegate('.js-hotlist-comment input', 'keyup', function (e) {
      if (e.which === 13) {
        var itemId = $(this).closest('.js-hotlist-comment').data('id');
        hotlist.updateComment(itemId, $(this).val());
        $(this).closest('.js-hotlist-comment').hide();
      }
    });

    // Wjem the comment button in any item is clicked
    $('.activity-list').delegate('.js-add-hotlist-comment', 'click', function () {
      var hotlistItem = $(this).closest('.js-activity-open-patient, .js-activity-closed-patient');
      var itemId = hotlistItem.data('id');
      var commentRow = hotlistItem.siblings('.js-hotlist-comment[data-id="' + itemId + '"]');

      if (commentRow.css('display') !== 'none') {
        hotlist.updateComment(itemId, commentRow.find('input').val());
        commentRow.hide();
      } else {
        commentRow.show();
        commentRow.find('input').focus();
      }

      return false;
    });
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
      }
    });
  };

  HotList.prototype.updateListCounters = function () {
    $('.patients-open .count').text($('.activity-list.open .js-activity-open-patient').length);
    $('.patients-closed .count').text($('.activity-list.closed .js-activity-closed-patient').length);
  };

  HotList.prototype.removeItem = function (itemId) {
    $('.activity-list tr[data-id="' + itemId + '"]').remove();
    $('body').find( ".oe-tooltip" ).remove();
  };


  HotList.prototype.updateComment = function (itemId, userComment) {
    var hotlistItem = $('.activity-list tr[data-id="' + itemId + '"]');
    var displayComment = userComment.substr(0, 20) + (userComment.length > 20 ? '...' : '');
    hotlistItem.find('.js-hotlist-comment-readonly').text(displayComment);

    $.ajax({
      type: 'GET',
      url: '/UserHotlistItem/updateUserComment',
      data: {hotlist_item_id: itemId, comment: userComment},
    });
  };

  exports.HotList = HotList;

}(OpenEyes.UI));