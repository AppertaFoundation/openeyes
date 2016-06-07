/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

(function (exports) {
  /**
   * OpenEyes UI namespace
   * @namespace OpenEyes.UI
   * @memberOf OpenEyes
   */
  var $searchInput = '';
  var dialog;

  var mergeSelect = function (event, ui) {

    if (Object.keys(patientMerge.patients.secondary).length === 0) {

      // check if the secondary and primary patient ids are the same
      if (patientMerge.patients.primary.id != ui.item.id) {
        patientMerge.patients.secondary = ui.item;
        patientMerge.updateDOM('secondary');
        if (patientMerge.patients.primary.id) {
          patientMerge.validatePatientsData(null, displayConflictMessage);
        }

      } else {
        // secondary and primary patient ids are the same - ALERT
        new OpenEyes.UI.Dialog.Alert({
          content: "Primary and Secondary patient cannot be the same record."
        }).open();
      }
    } else if (Object.keys(patientMerge.patients.primary).length === 0) {

      if (patientMerge.patients.secondary.id != ui.item.id) {
        patientMerge.patients.primary = ui.item;
        patientMerge.updateDOM('primary');

        if (patientMerge.patients.secondary.id) {
          patientMerge.validatePatientsData(null, displayConflictMessage);
        }

      } else {
        new OpenEyes.UI.Dialog.Alert({
          content: "Primary and Secondary patient cannot be the same record."
        }).open();
      }

    } else {

      $('<h2 class="text-center">Do you want to set this patient as Primary or Secondary ?</h2>').data('ui', ui).dialog({
        buttons: [
          {
            id: 'secondaryPatientBtn',
            class: 'disabled patient-mrg-btn',
            text: 'Secondary',
            click: function () {
              var ui = $(this).data('ui');
              if (patientMerge.patients.primary.id != ui.item.id) {
                patientMerge.patients.secondary = ui.item;
                patientMerge.updateDOM('secondary');
                patientMerge.validatePatientsData(null, displayConflictMessage);
                $(this).dialog("close");
              } else {
                $(this).dialog("close");
                $('<h2 title="Alert" class="text-center"></h2>').dialog();
                new OpenEyes.UI.Dialog.Alert({
                  content: "Primary and Secondary patient cannot be the same record."
                }).open();
              }
            }
          },
          {
            id: 'primaryPatientBtn',
            class: 'primary patient-mrg-btn',
            text: 'Primary',
            click: function () {
              var ui = $(this).data('ui');
              if (patientMerge.patients.secondary.id != ui.item.id) {
                patientMerge.patients.primary = ui.item;
                patientMerge.updateDOM('primary');
                patientMerge.validatePatientsData(null, displayConflictMessage);
                $(this).dialog("close");
              } else {
                $(this).dialog("close");
                new OpenEyes.UI.Dialog.Alert({
                  content: "Primary and Secondary patient cannot be the same record."
                }).open();
              }
            }
          }
        ],
        create: function () {
          var buttons = $('.ui-dialog-buttonset').children('button');
          buttons.removeClass("ui-widget ui-state-default ui-state-active ui-state-focus");
        }

      });

    }

    $('#patient_merge_search').val("");
    return false;
  };
  var mergeClose = function (event, ui) {
    if (($('.ui-menu li').length > 1 ) && (Object.keys(patientMerge.patients.primary).length === 0 || Object.keys(patientMerge.patients.secondary).length === 0)) {
      $("ul.ui-autocomplete").show();
    }
  };

  var singleSelect = function(event, uid){
    $('#patient-search').hide();
    $('#patient-result').html('<span>'+ uid.item.first_name + ' ' + uid.item.last_name +'</span>').show();
    $('#patient-result-id').val(uid.item.id);
  };

  var singleClose = function (event, uid) {

  };

  var selectFunction;
  var closeFunction;

  function autocomplete() {
    dialog = $searchInput.autocomplete({
      minLength: 0,
      source: function (request, response) {
        $.getJSON('/patientMergeRequest/search', {
          term: request.term,
          ajax: 'ajax',
        }, response);

      },
      search: function () {
        $('.loader').show();
      },
      select: selectFunction,
      response: function (event, ui) {
        $('.loader').hide();
        if (ui.content.length === 0) {
          $('.no-result-patients').slideDown();
        } else {
          $('.no-result-patients').slideUp();
        }
      },
      close: closeFunction
    });

    if (typeof dialog !== 'undefined' && dialog.length) {
      dialog.data("autocomplete")._renderItem = function (ul, item) {
        ul.addClass("z-index-1000");
        return $("<li></li>")
          .data("item.autocomplete", item)
          .append("<a><strong>" + item.first_name + " " + item.last_name + "</strong>" + " (" + item.age + ")" + "<span class='icon icon-alert icon-alert-" + item.gender.toLowerCase() + "_trans'>Male</span>" + "<div class='nhs-number'>" + item.nhsnum + "</div><br>Hospital No.: " + item.hos_num + "<br>Date of birth: " + item.dob + "</a>")
          .appendTo(ul);
      };
    }
  }

  exports.Search = {
    init: function ($input) {
      $searchInput = $input;
      autocomplete();
    },
    setMergeSelect: function(){
      selectFunction = mergeSelect;
      closeFunction = mergeClose;
    },
    setSingleSelect: function(){
      selectFunction = singleSelect;
      closeFunction = singleClose;
    }
  };
}(this.OpenEyes.UI));
