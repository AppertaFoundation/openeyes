/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

$(function () {

  var searchLoadingMsg = $('#search-loading-msg');
  var searchResults = $('#searchResults');

  // waitingList/index pagination buttons
  $(this).on('click', '.pagination a:not(.green.hint)', function (e) {
    e.preventDefault();
    e.stopPropagation();

    let url = new URL(`${window.location.protocol}//${window.location.host}${$(this).attr('href')}`);
    getAndRenderOperations(url, searchLoadingMsg, searchResults);
  });

  $(this).on('click', '#waitingList-filter button[type="submit"]', function (e) {
    e.preventDefault();

    let url = baseUrl + '/OphTrOperationbooking/waitingList/search';
    getAndRenderOperations(url, searchLoadingMsg, searchResults);
  });

  $(this).on('click', '#btn_print', function (e) {
    print_items_from_selector('input[id^="operation"]:checked', false);
    enableButtons();
  });

  $(this).on('click', '#btn_print_all', function () {
    print_items_from_selector('input[id^="operation"]:enabled', true);
    enableButtons();
  });

  $(this).on('change', 'input[id^="operation"]', function() {
    $('#btn_confirm_selected').toggleClass('disabled', $('input[id^="operation"]:checked').length === 0);
  });

  $(this).on('click', '#btn_confirm_selected', function (e) {
    var data = {};
    data['adminconfirmto'] = $('#adminconfirmto').val();
    data['adminconfirmdate'] = $('#adminconfirmdate').val();

    data['operations'] = [];
    $('input[id^="operation"]:checked').map(function () {
      data['operations'].push($(this).attr('id').replace(/operation/, ''));
    });

    if (data['operations'].length === 0) {
      new OpenEyes.UI.Dialog.Alert({
        content: 'No items selected.',
        onClose: function () {
          enableButtons();
        }
      }).open();
    } else {
      disableButtons();

      data['YII_CSRF_TOKEN'] = YII_CSRF_TOKEN;

      $.ajax({
        url: baseUrl + '/OphTrOperationbooking/waitingList/confirmPrinted',
        type: "POST",
        data: data,
        success: function (html) {
          enableButtons();
          $('#waitingList-filter button[type="submit"]').click();
        }
      });
    }

    e.preventDefault();
  });

  $('#patient_identifier_value').focus();

  if ($('#subspecialty-id').length) {
    if ($('#subspecialty-id').val() !== '') {
      var firm_id = $('#firm-id').val();
      $.ajax({
        url: baseUrl + '/OphTrOperationbooking/waitingList/filterFirms',
        type: "POST",
        data: "subspecialty_id=" + $('#subspecialty-id').val() + '&YII_CSRF_TOKEN=' + YII_CSRF_TOKEN,
        success: function (data) {
          $('#firm-id').attr('disabled', false);
          $('#firm-id').html(data);
          $('#firm-id').val(firm_id);
          $('#waitingList-filter button[type="submit"]').click();
        }
      });
    } else {
      $('#waitingList-filter button[type="submit"]').click();
    }
  }

  $('#firm-id').bind('change', function () {
    $.ajax({
      url: baseUrl + '/OphTrOperationbooking/waitingList/filterSetFirm',
      type: "POST",
      data: "firm_id=" + $('#firm-id').val() + "&YII_CSRF_TOKEN=" + YII_CSRF_TOKEN,
      success: function (data) {
      }
    });
  });

  $('#status').bind('change', function () {
    $.ajax({
      url: baseUrl + '/OphTrOperationbooking/waitingList/filterSetStatus',
      type: "POST",
      data: "status=" + $('#status').val() + "&YII_CSRF_TOKEN=" + YII_CSRF_TOKEN,
      success: function (data) {
      }
    });
  });

  $('#site_id').bind('change', function () {
    $.ajax({
      url: baseUrl + '/OphTrOperationbooking/waitingList/filterSetSiteId',
      type: "POST",
      data: "site_id=" + $('#site_id').val() + "&YII_CSRF_TOKEN=" + YII_CSRF_TOKEN,
      success: function (data) {
      }
    });
  });

  $('#patient_identifier_value').bind('keyup', function (e) {

      $.ajax({
        url: baseUrl + '/OphTrOperationbooking/waitingList/filterSetHosNum',
        type: "POST",
        data: "patient_identifier_value=" + $('#patient_identifier_value').val() + "&YII_CSRF_TOKEN=" + YII_CSRF_TOKEN,
        success: function (data) {
        }
      });

  });
});

function print_items_from_selector(sel, all) {
  var operations = new Array();

  var nogp = 0;

  var operations = $(sel).map(function (i, n) {
    var no_gp = $(n).parent().parent().hasClass('waitinglistOrange') && $(n).parent().html().match(/>NO GP</)

    if (no_gp) nogp += 1;

    if (!no_gp) {
      return $(n).attr('id').replace(/operation/, '');
    }
  }).get();

  if (operations.length == 0) {
    if (nogp == 0) {
      new OpenEyes.UI.Dialog.Alert({
        content: "No items selected for printing."
      }).open();
    } else {
      show_letter_warnings(nogp);
    }
  } else {
    show_letter_warnings(nogp);
    printIFrameUrl(baseUrl + '/OphTrOperationbooking/waitingList/printLettersPdf', {'operations': operations, 'all': all});
  }
}

function show_letter_warnings(nogp) {
  var msg = '';

  if (nogp > 0) {
    msg += nogp + " item" + (nogp == 1 ? '' : 's') + " could not be printed as the patient has no GP practice.";
  }

  if (msg.length > 0) {
    new OpenEyes.UI.Dialog.Alert({
      content: msg
    }).open();
  }
}

function getAndRenderOperations(url, loading_ele, result_ctn){
  loading_ele.show();
  result_ctn.empty();
  $('#btn_confirm_selected').addClass('disabled');

  $.ajax({
    'url': url,
    'type': 'POST',
    'data': $('#waitingList-filter').serialize(),
    'success': function (data) {
      result_ctn.html(data);
    },
    complete: function () {
      loading_ele.hide();
      enableButtons();
    }
  });
}
