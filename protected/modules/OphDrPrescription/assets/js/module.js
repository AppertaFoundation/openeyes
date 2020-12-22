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

$(document).ready(function () {

  const NORMAL_PRINT = '1';
  const FP10_PRINT = '2';
  const WP10_PRINT = '3';

  function checkPrescriptionLength() {
    var rowCount = $('#prescription_items tr').length;
    if (rowCount === 1) {
      new OpenEyes.UI.Dialog.Alert({
        content: "Items cannot be blank.",
        closeCallback: enableButtons // re-enable buttons when closing the popup
      }).open();

      return false;
    }

    return true;
  }

  autosize($('#Element_OphDrPrescription_Details_comments'));

  $(document).on('click', '#et_save_print, #et_save_print_footer', function (e) {
    $('#Element_OphDrPrescription_Details_draft').val(0);
    if (!checkPrescriptionLength()) {
      e.preventDefault();
    }
  });

  $(document).on('click', '#et_save_print_form, #et_save_print_form_footer', function (e) {
    $('#Element_OphDrPrescription_Details_draft').val(0);
    if (!checkPrescriptionLength()) {
      e.preventDefault();
    }
  });

    $(document).on('click', '#et_save, #et_save_footer', function (e) {
        $('#Element_OphDrPrescription_Details_draft').val(0);
        if (!checkPrescriptionLength()) {
          e.preventDefault();
        }
    });

    $(document).on('click', '#et_save_draft, #et_save_draft_footer', function (e) {
        $('#Element_OphDrPrescription_Details_draft').val(1);
        if (!checkPrescriptionLength()) {
            e.preventDefault();
        }
    });

    $(document).on('click', '#et_save_final, #et_save_final_footer', function (e) {
        var data = {
            YII_CSRF_TOKEN: YII_CSRF_TOKEN,
            element: $(this).data('element'),
            event: OE_event_id
        };
        disableButtons();
        $.ajax({
            'type': 'POST',
            'url': baseUrl + '/OphDrPrescription/default/finalize/',
            'data': data,
            'dataType':'json',
            'success': function ( response ) {
                if(response.success === 0){
                    new OpenEyes.UI.Dialog.Alert({
                        content: "There was an unexpected error save the prescription, please try again or contact support for assistance."
                    }).open();
                    enableButtons();
                } else {
                    window.location.reload();
                }
            }
        });

        e.preventDefault();
    });

  $(document).on('click', '#et_print', function (e) {
    if ($('#et_ophdrprescription_draft').val() == 1) {
      $.ajax({
        'type': 'GET',
        'url': baseUrl + '/OphDrPrescription/default/doPrint/' + OE_event_id,
        'success': function (html) {
          if (html.trim() == "1") {
            window.location.reload();
          } else {
            new OpenEyes.UI.Dialog.Alert({
              content: "There was an unexpected error printing the prescription, please try again or contact support for assistance."
            }).open();
          }
        }
      });
    } else {
      do_print_prescription();
      e.preventDefault();
    }
  });

  $(document).on('click', '#et_print_fp10', function (e) {
    if ($('#et_ophdrprescription_draft').val() == 1) {
      $.ajax({
        'type': 'GET',
        'url': baseUrl + '/OphDrPrescription/default/doPrint/' + OE_event_id + '?print_mode=FP10',
        'success': function (html) {
          if (html.trim() == "1") {
            window.location.reload();
          } else {
            new OpenEyes.UI.Dialog.Alert({
              content: "There was an unexpected error printing the prescription, please try again or contact support for assistance."
            }).open();
          }
        }
      });
    } else {
      do_print_fpTen('FP10');
      e.preventDefault();
    }
  });

  $(document).on('click', '#et_print_wp10', function (e) {
    if ($('#et_ophdrprescription_draft').val() == 1) {
      $.ajax({
        'type': 'GET',
        'url': baseUrl + '/OphDrPrescription/default/doPrint/' + OE_event_id + '?print_mode=WP10',
        'success': function (html) {
          if (html.trim() == "1") {
            window.location.reload();
          } else {
            new OpenEyes.UI.Dialog.Alert({
              content: "There was an unexpected error printing the prescription, please try again or contact support for assistance."
            }).open();
          }
        }
      });
    } else {
      do_print_fpTen('WP10');
      e.preventDefault();
    }
  });

  switch ($('#et_ophdrprescription_print').val()) {
    case NORMAL_PRINT:
      setTimeout(do_print_prescription(), 1000);
      break;
    case FP10_PRINT:
      setTimeout(do_print_fpTen('FP10'), 1000);
      break;
    case WP10_PRINT:
      setTimeout(do_print_fpTen('WP10'), 1000);
      break;
    default:
      break;
  }
});

function do_print_prescription() {
  $.ajax({
    'type': 'GET',
    'url': baseUrl + '/OphDrPrescription/default/markPrinted?event_id=' + OE_event_id,
    'success': function (html) {
      if (html.trim() == "1") {
        printIFrameUrl(OE_print_url, null);
      } else {
        new OpenEyes.UI.Dialog.Alert({
          content: "There was an error printing the prescription, please try again or contact support for assistance."
        }).open();
      }
      enableButtons();
    }
  });
}

function do_print_fpTen(print_mode) {
  $.ajax({
    'type': 'GET',
    'url': baseUrl + '/OphDrPrescription/default/markPrinted?event_id=' + OE_event_id,
    'success': function (html) {
      if (html.trim() == "1") {
        printIFrameUrl(OE_print_url + '?print_mode=' + print_mode + '&print_footer=false', null);
      } else {
        new OpenEyes.UI.Dialog.Alert({
          content: "There was an error printing the prescription, please try again or contact support for assistance."
        }).open();
      }
      enableButtons();
    }
  });
}

