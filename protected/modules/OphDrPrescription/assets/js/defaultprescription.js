// we need to initialize the list of drug items
if ($('#DrugSet_id').length > 0) {
  addSet($('#DrugSet_id').val());
}

// Add repeat to prescription
$('body').delegate('#repeat_prescription', 'click', function () {
  addRepeat();
  return false;
});

// Clear prescription
$('body').delegate('#clear_prescription', 'click', function () {
  clear_prescription();
  return false;
});

// Update drug route options for selected route if not admin page
$('body').delegate('select.drugRoute', 'change', function () {
  var selected = $(this).children('option:selected');
  var options_td = $(this).parent().next();
  if (options_td.attr("class") == 'route_option_cell') {
    var key = $(this).closest('tr').attr('data-key');
    $.get(baseUrl + "/OphDrPrescription/Default/RouteOptions", {
      key: key,
      route_id: selected.val()
    }, function (data) {
      options_td.html(data);
    });
  }
  return false;
});

// Remove item from prescription
$('#prescription_items').delegate('i.removeItem', 'click', function () {
  var row = $(this).closest('tr');
  var drug_id = row.find('input[name$="[drug_id]"]').first().val();
  var key = row.attr('data-key');
  $('#prescription_items tr[data-key="' + key + '"]').remove();
  return false;
});

// Add taper to item
$('#prescription_items').on('click', '.js-add-taper', function (event) {
  event.preventDefault();
  var row = $(this).closest('tr');
  var key = row.attr('data-key');
  var last_row = $('#prescription_items tr[data-key="' + key + '"]').last();
  var taper_key = (last_row.attr('data-taper')) ? parseInt(last_row.attr('data-taper')) + 1 : 0;
  var colspanNum = (controllerName == 'DefaultController') ? 2 : 0;
  // Clone item fields to create taper row
  var dose_input = row.find('td.prescriptionItemDose input').first().clone();
  dose_input.attr('name', dose_input.attr('name').replace(/\[dose\]/, "[taper][" + taper_key + "][dose]"));
  dose_input.attr('id', dose_input.attr('id').replace(/_dose$/, "_taper_" + taper_key + "_dose"));
  var frequency_input = row.find('td.prescriptionItemFrequencyId select').first().clone();
  frequency_input.attr('name', frequency_input.attr('name').replace(/\[frequency_id\]/, "[taper][" + taper_key + "][frequency_id]"));
  frequency_input.attr('id', frequency_input.attr('id').replace(/_frequency_id$/, "_taper_" + taper_key + "_frequency_id"));
  frequency_input.val(row.find('td.prescriptionItemFrequencyId select').val());
  var duration_input = row.find('td.prescriptionItemDurationId select').first().clone();
  duration_input.attr('name', duration_input.attr('name').replace(/\[duration_id\]/, "[taper][" + taper_key + "][duration_id]"));
  duration_input.attr('id', duration_input.attr('id').replace(/_duration_id$/, "_taper_" + taper_key + "_duration_id"));
  duration_input.val(row.find('td.prescriptionItemDurationId select').val());

  // Insert taper row
  var odd_even = (row.hasClass('odd')) ? 'odd' : 'even';
  var new_row = $('<tr data-key="' + key + '" data-taper="' + taper_key + '" class="prescription-tapier ' + odd_even + '"></tr>');
  new_row.append(
    $('<td></td>'),
    $('<td><i class="oe-i child-arrow small no-click pad"></i><em class="fade">then</em></td>'),
    $('<td></td>').append(dose_input),
    $('<td colspan="' + colspanNum + '"></td>'),
    $('<td></td>').append(frequency_input),
    $('<td></td>').append(duration_input),
    $('<td></td>'),
    $('<td></td>'),
    $('<td class="prescription-actions"><i class="oe-i trash removeTaper"></i></td>'));
  last_row.after(new_row);

  return false;
});

// Remove taper from item
$('#prescription_items').delegate('i.removeTaper', 'click', function () {
  var row = $(this).closest('tr');
  row.remove();
  return false;
});

$(' #prescription_items').delegate('select.dispenseCondition', 'change', function () {
  getDispenseLocation($(this));
  return false;
});

// remove all the rows from the prescription table
function clear_prescription() {
  $('#prescription_items tbody tr').remove();
}

// Add repeat to prescription
function addRepeat() {
  $.get(baseUrl + "/OphDrPrescription/Default/RepeatForm", {
    key: getNextKey(),
    patient_id: OE_patient_id
  }, function (data) {
    $('#prescription_items').find('tbody').append(data);
  });
}

function processSetEntries(set_id)
{
    $.get(baseUrl + "/OphDrPrescription/PrescriptionCommon/getSetDrugs", {
        set_id: set_id
    }, function (data) {
        let allergicDrugsController = new OpenEyes.OphDrPrescription.AllergicDrugsController(patientAllergies);
        allergicDrugsController.addEntries(JSON.parse(data));
        let allergicDrugs = allergicDrugsController.getAllergicDrugs();
        if (allergicDrugs) {
            let dialog = new OpenEyes.UI.Dialog.Confirm({
                content: "Patient is allergic to " +
                    allergicDrugs.join() +
                    ". Are you sure you want to add them?"
            });
            dialog.on('ok', function () {
                addSet(set_id);
            }.bind(this));
            dialog.open();
        } else {
            addSet(set_id);
        }
    });
}

// Add set to prescription
function addSet(set_id) {
  // we need to call different functions for admin and public pages here
  if (controllerName == 'DefaultController') {
    $.get(baseUrl + "/OphDrPrescription/PrescriptionCommon/SetForm", {
      key: getNextKey(),
      patient_id: OE_patient_id,
      set_id: set_id
    }, function (data) {
      $('#prescription_items').find('tbody').append(data);

    });
  } else {
    $.getJSON(baseUrl + "/OphDrPrescription/PrescriptionCommon/SetFormAdmin", {
      key: getNextKey(),
      set_id: set_id
    }, function (data) {
      $('#set_name').val(data.drugsetName);
      $('#subspecialty_id').val(data.drugsetSubspecialtyId);
      clear_prescription();
      $('#prescription_items').find('tbody').append(data.tableRows);
    });
  }
}

function addItemsWithoutAllergicDrugs(selectedItems , allergicDrugs)
{
   let selectedItemsWithoutAllergies = [];
   let newItemsCounter = 0;
    for (let index = 0; index < selectedItems.length; index++) {
        if(!allergicDrugs.includes(selectedItems[index]['label'])) {
            selectedItemsWithoutAllergies[newItemsCounter] = selectedItems[index];
            newItemsCounter++;
        }
    }
    addItems(selectedItemsWithoutAllergies);
}

function addItems(selectedItems){
    for (let index = 0; index < selectedItems.length; index++) {
        addItem(selectedItems[index].label, selectedItems[index].id);

    }
}

// Add item to prescription
function addItem(label, item_id) {
  // we need to call different functions for admin and public pages here
  $.ajaxSetup({async: false});
  if (controllerName == 'DefaultController') {
    $.get(baseUrl + "/OphDrPrescription/PrescriptionCommon/ItemForm", {
      key: getNextKey(),
      patient_id: OE_patient_id,
      drug_id: item_id,
    }, function (data) {
      $('#prescription_items').find('tbody').append(data);
    });
  } else {
    $.get(baseUrl + "/OphDrPrescription/PrescriptionCommon/ItemFormAdmin", {
      key: getNextKey(),
      drug_id: item_id,
    }, function (data) {
      $('#prescription_items').find('tbody').append(data);
    });
  }
}

// Get next key for adding rows
function getNextKey() {
  var last_item = $('#prescription_items .prescriptionItem').last();
  return (last_item.attr('data-key')) ? parseInt(last_item.attr('data-key')) + 1 : 0;
}

function getDispenseLocation(dispense_condition) {
  $.get(baseUrl + "/OphDrPrescription/PrescriptionCommon/GetDispenseLocation", {
    condition_id: dispense_condition.val(),
  }, function (data) {
    var dispense_location = dispense_condition.closest('.prescriptionItem').find('.dispenseLocation');
    dispense_location.find('option').remove();
    dispense_location.append(data);
    dispense_location.show();
  });
}

$(function () {
  new OpenEyes.UI.AdderDialog.PrescriptionDialog({
    openButton: $('#add-prescription-btn'),
    itemSets: [
        new OpenEyes.UI.AdderDialog.ItemSet([{'label': 'No preservative', 'id': '1'}], {'multiSelect': false, 'class': 'js-no-preservative'}),
        new OpenEyes.UI.AdderDialog.ItemSet(prescriptionElementCommonDrugs, {'multiSelect': true, 'class': 'js-drug-list'})
    ],
    searchOptions: {
      searchSource: searchListUrl,
      searchFilter: prescriptionElementDrugTypes,
    },
    width: 600,
    deselectOnReturn: false,
    onReturn: function (adderDialog, selectedItems) {
      
        if(typeof patientAllergies === 'undefined'){
          patientAllergies = false;    
        }

        let allergicDrugsController = new OpenEyes.OphDrPrescription.AllergicDrugsController(patientAllergies);
        allergicDrugsController.addEntries(selectedItems);
        let allergicDrugs = allergicDrugsController.getAllergicDrugs();
        if(allergicDrugs){
            let dialog = new OpenEyes.UI.Dialog.Confirm({
                content: "Patient is allergic to " +
                    allergicDrugs.join() +
                    ". Are you sure you want to add them?"
            });
            dialog.on('ok', function () {
                addItems(selectedItems);
            }.bind(this));

            dialog.on('cancel' , function(){
              addItemsWithoutAllergicDrugs(selectedItems , allergicDrugs);
            }.bind(this));
            dialog.open();
        } else {
            addItems(selectedItems);
        }

        $('.js-search-autocomplete').val("");
        $('.js-search-autocomplete').keyup();

    },
  });

  new OpenEyes.UI.AdderDialog({
    openButton: $('#add-standard-set-btn'),
    itemSets: [new OpenEyes.UI.AdderDialog.ItemSet(prescriptionDrugSets)],
    onReturn: function (adderDialog, selectedItems) {
      for (let i = 0; i < selectedItems.length; ++i) {
          processSetEntries(selectedItems[i].id);
      }
    }
  });
});

// Check for existing prescriptions for today - warn if creating only
$(document).ready(function () {
  if (window.location.href.indexOf("/update/") > -1) return;

  var error_count = $('a.errorlink').length;
  var today = $.datepicker.formatDate('dd-M-yy', new Date());
  var show_warning = false;
  var ol_list = $("body").find("ol.events");
  var prescription_count = 0;
  ol_list.each(function (idx, ol) {
    var li_list = $(ol).find("li");
    li_list.each(function (idx, li) {

      if ($(li).html().indexOf("OphDrPrescription/default/view") > 0) {
        var p_day = $(li).find("span.day").first().html();

        if (p_day.length < 2) {
          p_day = '0' + p_day;
        }
        var prescription_date =
          p_day + '-'
          + $(li).find("span.mth").first().html() + '-'
          + $(li).find("span.yr").html();

        if (today == prescription_date) {
          show_warning = true;
          prescription_count += 1;

        }
      }
    })
  })

  if (show_warning && error_count == 0) {
    var warning_message = 'Prescriptions have already been created for this patient today.';
    if (prescription_count == 1) {
      warning_message = 'A Prescription has already been created for this patient today.';
    }

    var p = $('#event-content');
    var position = p.position();
    // alert ('L->'+position.left+ ' T '+position.top);
    var topdist = position.left + 400;
    var leftdist = position.top + 500;

    var dialog_msg = '<div class="ui-dialog ui-widget ui-widget-content ui-corner-all dialog" id="dialog-msg" tabindex="-1" role="dialog" aria-labelledby="ui-id-1" style="outline: 0px; height: auto; width: 600px;  position: fixed; top: 50%; left: 50%; margin-top: -110px; margin-left: -200px;">' +
      '<div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">' +
      '<span id="ui-id-1" class="ui-dialog-title">Confirm Prescription</span>' +
      '</div><div id="site-and-firm-dialog" class="ui-dialog-content ui-widget-content" scrolltop="0" scrollleft="0" style="display: block; width: auto; min-height: 0px; height: auto;">' +
      '<div class="alert-box alert with-icon"> <strong>WARNING: ' + warning_message + ' </strong></div>' +
      '<p>Do you want to continue with a new prescription?</p>' +
      '<div style = "margin-top:20px; float:right">' +
      '<input class="secondary small" id="prescription-yes" type="submit" name="yt0" style = "margin-right:10px" value="Yes" onclick="hide_dialog()">' +
      '<input class="warning small" id="prescription-no" type="submit" name="yt0" value="No" onclick="goBack()">' +
      '</div>';

    var blackout_box = '<div id="blackout-box" style="position:fixed;top:0;left:0;width:100%;height:100%;background-color:black;opacity:0.6;">';


    $(dialog_msg).prependTo("body");
    $(blackout_box).prependTo("body");
    $('div#blackout_box').css.opacity = 0.6;
    $("input#prescription-no").focus();
    $("input#prescription-yes").keyup(function (e) {
      hide_dialog();
    });
  }

});

function hide_dialog() {
  $('#blackout-box').hide();
  $('#dialog-msg').hide();
}

function goBack() {
  window.history.back();
}

// Add comments to item
$('#prescription_items').on('click', '.js-add-comments', function () {
  var $row = $(this).closest('tr');
  var key = $row.attr('data-key');
  $('#comments-' + key).show();
  autosize($('.js-input-comments'));
  $row.find('.js-add-comments').hide();
  return false;
});
// Remove comments from item
$('#prescription_items').on('click', '.js-remove-add-comments', function () {
  var $row = $(this).closest('tr');
  var key = $row.attr('data-key');
  $('#comments-' + key).hide();
  $row.find('.js-add-comments').show();
  return false;
});
