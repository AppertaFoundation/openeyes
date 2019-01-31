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

  function checkPrescriptionLength() {
    var rowCount = $('#prescription_items tr').length;
    if (rowCount === 1) {
      new OpenEyes.UI.Dialog.Alert({
        content: "Items cannot be blank."
      }).open();

      return false;
    }

    return true;
  }

  $('#Element_OphDrPrescription_Details_comments').autosize();

  $(document).on('click', '#et_save_print', function (e) {
    $('#Element_OphDrPrescription_Details_draft').val(0);
    if (!checkPrescriptionLength()) {
      e.preventDefault();
    }
  });

    $(document).on('click', '#et_save', function (e) {
        $('#Element_OphDrPrescription_Details_draft').val(0);
        if (!checkPrescriptionLength()) {
          e.preventDefault();
        }
    });
    
    $(document).on('click', '#et_save_draft', function (e) {
        $('#Element_OphDrPrescription_Details_draft').val(1);
        if (!checkPrescriptionLength()) {
            e.preventDefault();
        }
    });
    
    $(document).on('click', '#et_save_final', function (e) {
        var data = {
            YII_CSRF_TOKEN: YII_CSRF_TOKEN,
            element: $(this).data('element'),
            event: OE_event_id
        };
        
        $.ajax({
            'type': 'POST',
            'url': baseUrl + '/OphDrPrescription/default/finalize/',
            'data': data,
            'dataType':'json',
            'success': function ( response ) {
                if(response.success === 0){
                    new OpenEyes.UI.Dialog.Alert({
                        content: "Still not working!"
                    }).open();
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
          if (html == "1") {
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

  if ($('#et_ophdrprescription_print').val() == 1) {
    setTimeout("do_print_prescription();", 1000);
  }
});

function do_print_prescription() {
  $.ajax({
    'type': 'GET',
    'url': baseUrl + '/OphDrPrescription/default/markPrinted?event_id=' + OE_event_id,
    'success': function (html) {
      if (html == "1") {
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

