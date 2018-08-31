/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2017
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

var OpenEyes = OpenEyes || {};

OpenEyes.OphCiExamination = OpenEyes.OphCiExamination || {};

(function(exports) {
  function HistoryMedicationsController(options) {
    this.options = $.extend(true, {}, HistoryMedicationsController._defaultOptions, options);
    this.$element = this.options.element;
    this.$table = this.$element.find('table.entries');
    this.templateText = this.$element.find('.entry-template').text();
    this.fields = [
      'medication_name',
      'ref_medication_id',
      'dose',
        'dose_unit_term',
      'frequency_id',
      'route_id',
      'laterality',
      'start_date',
      'end_date',
      'stop_reason_id',
      'usage_type',
        'group',
        'hidden',
        'prescription_item_id'
    ];
    this.drugsByRisk = {};
    this.initialiseFilters();
    this.initialiseTriggers();
    this.initialiseDatepicker();

    this.$element.data("controller_instance", this);

    this.options.onInit(this);

      window.switch_alternative = window.switch_alternative || function(anchor) {
          var $wrapper = $(anchor).closest(".alternative-display-element");
          $wrapper.hide();
          $wrapper.siblings(".alternative-display-element").show();
      };

  }

  HistoryMedicationsController._defaultOptions = {
    modelName: 'OEModule_OphCiExamination_models_HistoryMedications',
    element: undefined,
    addButtonSelector: '.js-add-select-search',
    removeButtonSelector: 'button.remove',
    searchSource: '/medication/finddrug',
    routeOptionSource: '/medication/retrieveDrugRouteOptions',
    searchAsTypedPrefix: 'As typed: ',
    drugFieldSelector: 'input[name$="[drug_id]"]',
    medicationFieldSelector: 'input[name$="[medication_drug_id]"]',
    asTypedFieldSelector: 'input[name$="[medication_name]"]',
    medicationSearchSelector: 'input[name$="[medication_search]"]',
    drugSelectSelector: 'select[name$="[drug_select]"]',
    medicationNameSelector: '.medication-name',
    medicationDisplaySelector: '.medication-display',
    startDateButtonSelector: '.start-medication.enable',
    cancelStartDateButtonSelector: '.start-medication.cancel',
    stopDateFieldSelector: 'input[name$="[stop_date]"]',
    stopDateButtonSelector: '.stop-medication.enable',
    cancelStopDateButtonSelector: '.stop-medication.cancel',
    routeFieldSelector: 'select[name$="[route_id]"]',
    routeOptionWrapperSelector: '.admin-route-options',

      // Customizable callbacks

      onInit: function(){},
      onControllerBinded: function (controller, name){},
      onAddedEntry: function($row, controller){},
      onRemovedEntry: function ($row, controller) {}
  };

  HistoryMedicationsController.prototype.bindController = function(controller, name) {
    this[name] = controller;
    this.boundController = controller;
    this.options.onControllerBinded(controller, name);
  };

    HistoryMedicationsController.prototype.unbindController = function(controller, name) {
        delete this[name];
        delete this.boundController;
        $.each(this.$table.find("tbody").children("tr"), function(i, e){
           $.removeData($(e), "bound_entry");
        });
    };

  /**
   * Setup datepicker
   */
  HistoryMedicationsController.prototype.initialiseDatepicker = function () {
    var row_count = OpenEyes.Util.getNextDataKey( this.$element.find('table tbody tr'), 'key');
    for (var i=0; i < row_count; i++){
      this.constructDatepicker('#'+this.options.modelName+'_datepicker_1_'+i);
      this.constructDatepicker('#'+this.options.modelName+'_datepicker_2_'+i);
    }
  };

  HistoryMedicationsController.prototype.setDatepicker = function () {
    var row_count = OpenEyes.Util.getNextDataKey( this.$element.find('table tbody tr'), 'key')-1;
    this.constructDatepicker('#'+this.options.modelName+'_datepicker_1_'+row_count);
    this.constructDatepicker('#'+this.options.modelName+'_datepicker_2_'+row_count);
  };

  HistoryMedicationsController.prototype.constructDatepicker = function (name) {
/*
    var datepicker= $(this.$table).find(name);
    if (datepicker.length!=0){
      pickmeup(name, {
        format: 'Y-m-d',
        hide_on_select: true,
        default_date: false
      });

      datepicker.on("pickmeup-change", function(e){$(e.target).trigger("change");});
    }
*/
  };

  /**
  * Sets up the filter controls on the table.
  */
  HistoryMedicationsController.prototype.initialiseFilters = function()
  {

    // if there aren't any stopped medications, then the filter is irrelevant
    if (!this.$table.find('tr.stopped').length) {
      this.$element.find('.show-stopped').hide();
      this.$element.find('.hide-stopped').hide();
    } else {
      this.hideStopped();
    }
  };

  HistoryMedicationsController.prototype.initialiseTriggers = function()
  {
    var controller = this;

    // removal button for table entries
    controller.$table.on('click', controller.options.removeButtonSelector, function(e) {
      e.preventDefault();
      // var key = $(e.target).closest("tr").attr("data-key");
      var $row = $(e.target).parents('tr');
      if($row.data('bound_entry') !== undefined) {
          $row.data('bound_entry').remove();
      }
      $row.remove();
      controller.options.onRemovedEntry($(e.target).closest("tr"), controller);
    });

    // adding entries
    controller.$element.on('click', controller.options.addButtonSelector, function(e) {
      e.preventDefault();
      controller.addEntry();
    });

    // setup current table row behaviours
    controller.$table.find('tbody tr').each(function() {
      controller.initialiseRow($(this));
    });

    controller.$element.on('click', '.show-stopped', function(e) {
        e.preventDefault();
        controller.showStopped();
    });

    controller.$element.on('click', '.hide-stopped', function(e) {
        e.preventDefault();
        controller.hideStopped();
    });
  };

  HistoryMedicationsController.prototype.initialiseRow = function($row)
  {
      var controller = this;
      controller.initialiseSearch($row.find('input.search'));

      $row.on('change', controller.options.drugSelectSelector, function(e) {
          var $option = $(this).find('option:selected'),
              tags = "" + $option.data('tags');
          controller.selectMedication($(this).parents('td'), {
              value: $option.val(),
              label: $option.text(),
              name: $option.data('tallmanlabel'),
              type: 'd', // only have pre-selected drugs available at the moment.
              tags: tags.split(',')
          });
      });

      $row.on('click', '.medication-rename', function(e) {
          e.preventDefault();
          controller.resetSearchRow($row, true);
      });

      $row.on('change', controller.options.routeFieldSelector, function(e) {
          controller.updateRowRouteOptions($row);
      });

      $row.on('change', '.fuzzy-date select', function(e) {
          var $fuzzyFieldset = $(this).closest('fieldset');
          var date = controller.dateFromFuzzyFieldSet($fuzzyFieldset);
          $fuzzyFieldset.find('input[type="hidden"]').val(date);
      });

      controller.setDatepicker();

      if (!$row.find(controller.options.medicationNameSelector).text().length) {
        controller.resetSearchRow($row, true);
      }

      $row.on("click", ".meds-stop-btn", function (e) {
          var $this_row = $(e.target).closest('tr');
          var $bound_row = $this_row.data('bound_entry');
          controller.setRowAsStopped($this_row);
            if(typeof $bound_row !== "undefined" && typeof controller.boundController !== "undefined") {
                controller.boundController.setRowAsStopped($bound_row);
            }
      });

      $row.on("click", ".meds-stop-cancel-btn", function(e) {
          var $this_row = $(e.target).closest('tr');
          var $bound_row = $this_row.data('bound_entry');
          controller.cancelStopped($this_row);
          if(typeof $bound_row !== "undefined" && typeof controller.boundController !== "undefined") {
              controller.boundController.cancelStopped($bound_row);
          }
      });

      $row.on("click", '.chk_prescribe', function (e) {
          var $this_row = $(e.target).closest('tr');
          var $newrow;
          var $prescribed_row;

          if($(e.target).attr("disabled") === "disabled") {
              return false;
          }

          if($(e.target).prop("checked")) {
              if($row.closest("tbody").hasClass("closed")) {
                  $newrow = controller.copyRow($row, controller.$table.find('tbody.entry_table_body.prescribed'));
              }
              else {
                  $newrow = controller.moveRow($row, controller.$table.find('tbody.entry_table_body.prescribed'));
              }
              $newrow.find('.chk_prescribe').attr("checked", "checked").attr("disabled", "disabled");
              $this_row.attr("data-prescribed-row", $newrow.attr("data-key"));
          }
          else {
              var key = $this_row.attr("data-prescribed-row");
              if(typeof key !== "undefined") {
                  $prescribed_row = controller.$table.find("tr[data-key="+key+"]");
                  $prescribed_row.remove();
              }
          }
      });

      $row.on("focus", ".dose, .frequency, .route, .laterality", function(e){
          $(e.target).attr("data-oldvalue", $(e.target).val());
      });

      $row.on("change", ".laterality", function(e){
          if(!$row.closest("tbody").hasClass("current") || $row.hasClass("donotsplit")) {
              return false;
          }
          var $lat = $(e.target);
          if($lat.attr("data-oldvalue") == 3 && $lat.val() != "") {
              var confirm = new OpenEyes.UI.Dialog.Confirm({
                  content: 'Do you want to create two separate entries for left and right eye?',
                  okButton: 'Yes',
                  cancelButton: 'No'
              });
              confirm.on("ok", function(){
                  var $target = $row.closest("tbody");
                  var $newrow = controller.copyRow($row, $target, true).insertAfter($row).addClass("donotsplit");
                  var new_laterality = $lat.val() == 1 ? 2 : 1;
                  $newrow.find(".laterality").val(new_laterality);
              });
              confirm.open();
          }
      });

      $row.on("change", ".dose, .frequency, .route, .laterality, .stop-reason, .end-date, .fuzzy-date select", function(e) {
          controller.updateBoundEntry($row);
          if($(e.target).hasClass("route") && typeof $row.data('bound_entry') !== 'undefined') {
              controller.updateRowRouteOptions($row.data('bound_entry'));
          }
      });

      $row.on("click", ".icon-switch i", function(e){
          var $icon = $(e.target);
          var $input = $icon.parent(".icon-switch").find("input");
          if($icon.attr("data-value") == 0) {
              $icon.attr("data-value", 1);
              $icon.css('opacity','1');
              $input.val(1);
          }
          else {
              $icon.attr("data-value", 0);
              $icon.css('opacity','0.5');
              $input.val(0);
          }
      });

      $row.find(".js-medication-search-autocomplete").autocomplete({
          minLength: 2,
          delay: 700,
          source: '/MedicationManagement/findRefMedications?ref_set_id=29',
          select: function(event, ui){
              $row.find(".medication-name .textual-display").text(ui.item.preferred_term);
              switch_alternative($(event.target));
              $row.find(".dose-unit-term").text(ui.item.dose_unit_term === null ? "" : ui.item.dose_unit_term);
              $row.find(".dose").val(ui.item.dose);
              $row.find(".frequency").val(ui.item.frequency_id);
              $row.find(".route").val(ui.item.route_id);
              $row.find(".ref_medication_id").val(ui.item.id);
              $row.find("input.medication-name").val(ui.item.preferred_term);

              if(!ui.item.will_copy) {
                  $row.addClass("ignore");
              }
              else {
                  $row.removeClass("ignore");
              }

              if(typeof controller.MMController !== "undefined") {
                  if(ui.item.will_copy) {
                      var $new_row = controller.MMController.addEntry(ui.item, false);
                      controller.bindEntries($row, $new_row);
                  }
                  else {
                      controller.removeBoundEntry($row);
                  }

              }

              controller.updateRowRouteOptions($row);
              controller.processRisks(ui.item);
          }
      });
  };

  HistoryMedicationsController.prototype.setRowAsStopped = function ($row) {

      $row.find(".end-date-column").find(".alternative-display-element").show();
      $row.find(".end-date-column").find(".alternative-display-element.textual").hide();

      var $rx_input = $row.find(".btn-prescribe input");
      $rx_input.attr("data-prev-state", $rx_input.prop("checked") ? '1' : '0');
      $rx_input.prop("checked", false).attr("disabled", "disabled");

      this.updateInputFromFuzzySelect($row);
  };

  HistoryMedicationsController.prototype.cancelStopped = function($row) {


      $row.find(".end-date-column").find(".alternative-display-element").hide();
      $row.find(".end-date-column").find(".alternative-display-element.textual").show();

      var $stop_reason = $row.find('.stop-reason');
      var $end_date_wrapper =  $row.find('.end_date_wrapper');

      $end_date_wrapper.find('select').val('');
      $stop_reason.val("");

      $row.find(".end.date").val("");

      var $rx_input = $row.find(".btn-prescribe input");
      state = $rx_input.attr("data-prev-state");
      $rx_input.attr("data-prev-state", $rx_input.prop("checked") ? '1' : '0');
      $rx_input.prop("checked", state === "1").removeAttr("disabled");
  };

  HistoryMedicationsController.prototype.copyRow = function($origin, $target, old_values)
  {
      var $row = $(this.createRow());
      $row.appendTo($target);
      var data = this.getRowData($origin, old_values);
      data.usage_type = $target.attr("data-usage-type");
      this.setRowData($row, data, ['start_date', 'end_date']);
      this.initialiseRow($row);

      return $row;
  };

  HistoryMedicationsController.prototype.moveRow = function($origin, $target)
  {
      var $newrow = this.copyRow($origin, $target);
      $origin.remove();
      return $newrow;
  };

    /**
     * Extract data from row
     * @param $row
     * @param old_values If set to true, data is extracted from the controls' previous values (if available)
     * @returns {{}}
     */

  HistoryMedicationsController.prototype.getRowData = function($row, old_values)
  {
      var rc = $row.attr("data-key");
      var obj = {};
      $.each(this.fields, function(i, field){
          var $element = $row.find("[name$='[entries]["+rc+"]["+field+"]']");
          if(typeof old_values !== "undefined" && old_values) {
              var oldval = $element.attr("data-oldvalue");
              obj[field] = typeof oldval !== "undefined" ? oldval : $element.val();
          }
          else {
              obj[field] =  $element.val();
          }
      });

      return obj;
  };

  HistoryMedicationsController.prototype.setRowData = function ($row, data, excl_fields)
  {
      var self = this;
      var rc = $row.attr("data-key");
      $.each(this.fields, function(i, field){
          if(typeof excl_fields === 'undefined' || excl_fields.indexOf(field) === -1) {
              if(typeof data[field] !== "undefined") {
                  var $input = $("[name='"+self.options.modelName+"[entries]["+rc+"]["+field+"]']");
                  $input.val(data[field]);
              }
          }
      });

      // Make copied row point to parent and vice versa
      if(typeof data.bound_entry !== "undefined") {
          self.bindEntries($row, data.bound_entry);
      }

      $row.find(".rgroup").val($row.closest("tbody").attr("data-group"));
      $row.find('.medication-name .textual-display').text(data.medication_name);
      $row.find(".dose-unit-term").text(data.dose_unit_term);

      self.updateFuzzySelect($row);
      self.switchRowToTextualDisplay($row);
  };

  HistoryMedicationsController.prototype.bindEntries = function($row1, $row2)
  {
      $row1.data("bound_entry", $row2);
      // Only one-way binding for now
      //$row2.data("bound_entry", $row1);
  };

  HistoryMedicationsController.prototype.updateBoundEntry = function ($row, callback)
  {
      var $bound_entry = $row.data("bound_entry");
      if(typeof $bound_entry === "undefined") {
          return;
      }
      var data = this.getRowData($row);
      var controller = $bound_entry.closest(".element-fields").data("controller_instance");
      controller.setRowData($bound_entry, data);
      controller.syncTextualDisplay($bound_entry);
      controller.updateFuzzySelect($bound_entry);

      if(callback !== undefined) {
          callback($bound_entry, controller);
      }
  };

    HistoryMedicationsController.prototype.removeBoundEntry = function($row)
    {
        var $bound_entry = $row.data("bound_entry");
        if(typeof $bound_entry === "undefined") {
            return;
        }

        $bound_entry.remove();
    };

  HistoryMedicationsController.prototype.switchRowToTextualDisplay = function($row)
  {
      this.syncTextualDisplay($row);
      var $cell = $row.find(".dose-frequency-route, .medication-display");
      $cell.find(".alternative-display-element.textual").show();
      $cell.find(".alternative-display-element").not(".textual").hide();
      $cell.find(".alt-display-trigger").show();
  };

  HistoryMedicationsController.prototype.syncTextualDisplay = function($row)
  {
      var dose_unit_term = $row.find(".dose").val()+" "+$row.find(".dose_unit_term").val();
      var frequency = $row.find(".frequency").val() !== "" ? $row.find(".frequency option:selected").text() : "";
      var route_lat = $row.find(".route").val() !== "" ? $row.find(".laterality option:selected").text().replace("-Select-", "") + " " + $row.find(".route option:selected").text() : "";

      $row.find(".textual-display-dose").text(dose_unit_term);
      $row.find(".textual-display-frequency").text(frequency);
      $row.find(".textual-display-route-laterality").text(route_lat);
  };

  HistoryMedicationsController.prototype.freezeRow = function($row) {
      // TODO
  };

  HistoryMedicationsController.prototype.initialiseSearch = function($el)
  {
    var controller = this;
    if (!$el.data('search')) {
        $el.autocomplete({
            minLength: 3,
            delay: 300,
            source: function(request, response) {
                $.getJSON(controller.options.searchSource, {
                    term: request.term,
                    ajax: 'ajax'
                }, response);
            },
            focus: function (event, ui) {
                event.preventDefault();
                if (event.hasOwnProperty('key')) {
                    $el.val(controller.getItemDisplayValue(ui.item));
                }
                // otherwise do nothing as this is a mouse hover focus;
                return false;
            },
            select: function (event, ui) {
                controller.searchSelect($el, event, ui);
            },
            response: function (event, ui) {
                ui.content.push({
                    value: $el.val(),
                    label: controller.options.searchAsTypedPrefix + $el.val(),
                    type: 't'
                });
            }
        });
        $el.autocomplete("widget").css('max-height', '150px').css('overflow', 'auto');
    }
  };

  HistoryMedicationsController.prototype.getItemDisplayValue = function(item)
  {
      if (item.type == 't') {
          return item.label.replace(this.options.searchAsTypedPrefix, '');
      }
      return item.label;
  };

  HistoryMedicationsController.prototype.searchSelect = function($el, event, ui)
  {
    event.preventDefault();

    var $container = $el.parents('td');
    this.resetSearchRow($container, false);

    this.selectMedication($container, ui.item);
    // set the search text box to the full value chosen
    $el.val(this.getItemDisplayValue(ui.item));
  };

  HistoryMedicationsController.prototype.selectMedication = function($container, item)
  {
      var displayText = this.getItemDisplayValue(item);

      if (item.type == 't') {
          $container.find(this.options.asTypedFieldSelector).val(item.value);
      }
      else if (item.type == 'd') {
          $container.find(this.options.drugFieldSelector).val(item.value);
          this.loadDrugDefaults($container.parents('tr'), item);
      } else {
          $container.find(this.options.medicationFieldSelector).val(item.value);
      }
      $container.find(this.options.medicationNameSelector).text(displayText);

      $container.find(this.options.medicationDisplaySelector).show();
      $container.find(this.options.medicationSearchSelector).hide();
      $container.find(this.options.drugSelectSelector).hide();

      this.processRisks(item);
  };

  HistoryMedicationsController.prototype.loadDrugDefaults = function($row, item)
  {
      $.getJSON('/medication/drugdefaults', { drug_id: item.value }, function (res) {
          for (var name in res) {
              if (name === 'dose') {
                  $row.find('[name$="[' + name +']"]').attr('placeholder', res['dose_unit']);
                  $row.find('[name$="[units]"]').val(res['dose_unit']);
              } else {
                  $row.find('[name$="[' + name +']"]').val(res[name]).change();
              }
          }
      });
  };

  /**
   * From the tags on the given item, retrieve the associated risks and update the core
   * register accordingly.
   *
   * @param item
   */
  HistoryMedicationsController.prototype.processRisks = function(item)
  {
     var self = this;
      $.getJSON('/OphCiExamination/Risks/forRefMedication/'+item.id, function (res) {
          self.addDrugForRisks(item.preferred_term, res);
      });
  };

  /**
   * send this drug name and associated risks to the core manager for inclusion in history risks
   *
   * @param drugName
   * @param risks
   */
  HistoryMedicationsController.prototype.addDrugForRisks = function(drugName, risks)
  {
      var risksMap = [];
      for (var i in risks) {
          if (risks.hasOwnProperty(i)) {
              risksMap.push({id: risks[i], comments: [drugName]});
          }
      }

      //checking the risksMap.length because HistoryRisksCore (js class) will automatically open the element if it isn't there
      if(risksMap.length){
          exports.HistoryRisks.addRisksForSource(risksMap, 'Medications');
      }

  };

  HistoryMedicationsController.prototype.resetSearchRow = function($container, showSearch)
  {
      if (showSearch === undefined)
          showSearch = true;

      $container.find(this.options.asTypedFieldSelector).val('');
      $container.find(this.options.drugFieldSelector).val('');
      $container.find(this.options.medicationFieldSelector).val('');
      $container.find(this.options.medicationNameSelector).text('');
      $container.find(this.options.drugSelectSelector).val('');
      if (showSearch) {
          $container.find(this.options.medicationDisplaySelector).hide();
          $container.find(this.options.medicationSearchSelector).show();
          $container.find(this.options.drugSelectSelector).show();
      }
  };

    HistoryMedicationsController.prototype.updateInputFromFuzzySelect = function($row)
    {
        $row.find(".fuzzy-date").each(function(i, fieldset) {
            var $hidden_input = $(fieldset).find("input[type=hidden]");
            var value = $(fieldset).find(".fuzzy_year").val()+''+$(fieldset).find(".fuzzy_month").val().padStart(2, "0")+''+$(fieldset).find(".fuzzy_day").val().padStart(2, "0");
            if(value != '00000000') {
                $hidden_input.val(value);
            }
            else {
                $hidden_input.val('');
            }
        });
    };

    HistoryMedicationsController.prototype.updateFuzzySelect = function($row)
    {
        $row.find(".fuzzy-date").each(function(i, fieldset) {
            var $hidden_input = $(fieldset).find("input[type=hidden]");
            if($hidden_input.length > 0) {
                var date = $hidden_input.val();
                if(date !== "") {
                    $(fieldset).find(".fuzzy_year").val(date.substr(0, 4));
                    $(fieldset).find(".fuzzy_month").val(parseInt(date.substr(4, 2)));
                    $(fieldset).find(".fuzzy_day").val(parseInt(date.substr(6, 2)));
                }
            }
        });
    };

  HistoryMedicationsController.prototype.showDate = function($row, $type)
  {
    var $wrapper = $row.find('.' + $type + '-date-wrapper');
    $wrapper.show();
    var $fuzzyFieldset = $wrapper.parents('fieldset');
    var date = this.dateFromFuzzyFieldSet($fuzzyFieldset);
    $fuzzyFieldset.find('input[type="hidden"]').val(date);
    $fuzzyFieldset.find('.enable').hide();
    $fuzzyFieldset.find('.cancel').show();
  }

  HistoryMedicationsController.prototype.cancelDate = function($row, $type)
  {
    var $wrapper = $row.find('.' + $type + '-date-wrapper');
    $wrapper.hide();
    var $fuzzyFieldset = $wrapper.parents('fieldset');
    $fuzzyFieldset.find('input[type="hidden"]').val('');
    $fuzzyFieldset.find('.enable').show();
    $fuzzyFieldset.find('.cancel').hide();
  };

  HistoryMedicationsController.prototype.updateRowRouteOptions = function($row)
  {
      var $routeOptionWrapper = $row.find(this.options.routeOptionWrapperSelector);
      $routeOptionWrapper.hide();
      $routeOptionWrapper.find('option').each(function() {
          if ($(this).val().length) {
              $(this).remove();
          }
      });
      var value = $row.find(this.options.routeFieldSelector + ' option:selected').val();
      if (value != "") {
          $.getJSON(this.options.routeOptionSource, {route_id: value}, function(data) {
              if (data.length) {
                  var $select = $routeOptionWrapper;
                  $.each(data, function(i, item) {
                    $select.append('<option value="' + item.id +'">' + item.name + '</option>');
                  });
                  $routeOptionWrapper.show();
              }
          });
      }
  };

  HistoryMedicationsController.prototype.createRow = function(data)
  {
    if (data === undefined)
      data = {};

    data['row_count'] = OpenEyes.Util.getNextDataKey( this.$element.find('table tbody tr'), 'key');
    return Mustache.render(
      this.templateText,
      data
    );
  };


  HistoryMedicationsController.prototype.addEntry = function(medication, do_callback)
  {
      if(do_callback === undefined) {
          do_callback = true;
      }

    var row = this.createRow();
    var $row = $(row);
    $row.data("medication_data", medication);

    $row.find(".rgroup").val("new");

    if(this.$table.find('tbody').find("tr.js-divider").length > 0) {
        $row.insertBefore(this.$table.find('tbody').find("tr.js-divider"));
    }
    else {
        $row.appendTo(this.$table.find('tbody'));
    }
    this.initialiseRow($row);

      if (typeof medication !== 'undefined') {
        $row.find('.medication-display').text(medication.preferred_term);
        $row.find('.medication-name').val(medication.preferred_term);
        $row.find('.ref_medication_id').val(medication.id);

          this.setRowData($row, {
              dose: medication.dose !== null ? medication.dose : 1,
              frequency_id: medication.frequency_id,
              route_id: medication.route_id,
              group: 'new'
          });

          $row.find(".dose-unit-term").text(medication.dose_unit_term !== null ? medication.dose_unit_term : "");

          this.updateRowRouteOptions($row);
      }

      if(do_callback) {
          this.options.onAddedEntry($row, this);
      }

    return $row;
  };


    /**
     * @TODO: should be common function across history elements
     * @param fieldset
     * @returns {*}
     */
    HistoryMedicationsController.prototype.dateFromFuzzyFieldSet = function(fieldset)
    {
        var res = fieldset.find('select.fuzzy_year').val();
        var month = parseInt(fieldset.find('select.fuzzy_month option:selected').val());
        res += '' + ((month < 10) ? '0' + month.toString() : month.toString());
        var day = parseInt(fieldset.find('select.fuzzy_day option:selected').val());
        res += '' + ((day < 10) ? '0' + day.toString() : day.toString());

        return res;
    };

    HistoryMedicationsController.prototype.showStopped = function()
    {
        this.$table.find('tr.stopped').show();
        this.$element.find('.show-stopped').hide();
        this.$element.find('.hide-stopped').show();
    };

    HistoryMedicationsController.prototype.hideStopped = function()
    {
        this.$table.find('tr.stopped').hide();
        this.$element.find('.show-stopped').show();
        this.$element.find('.hide-stopped').hide();
    };

    HistoryMedicationsController.prototype.setDoNotSaveEntries = function(v)
    {
        this.$element.find(".do_not_save_entries").val(v ? "1" : "0");
    };


  exports.HistoryMedicationsController = HistoryMedicationsController;
})(OpenEyes.OphCiExamination);

(function(exports) {
  function HistoryMedicationsViewController(options) {
    this.options = $.extend(true, {}, HistoryMedicationsViewController._defaultOptions, options);
    this.$element = this.options.element;
    this.initialiseTriggers();
  }

  HistoryMedicationsViewController._defaultOptions = {
    modelName: 'OEModule_OphCiExamination_models_HistoryMedications',
    element: undefined,
    detailToggleSelector: '.detail-toggle',
    kindToggleSelector: '.kind-toggle'
  };

  HistoryMedicationsViewController.prototype.initialiseTriggers = function()
  {
    var controller = this;

    controller.$element.on('click', controller.options.detailToggleSelector, function(e) {
      e.preventDefault();
      var $dataDisplay = controller.$element.find('.' + $(this).data('kind'));
      $dataDisplay.find('.detail').toggle();
      $dataDisplay.find('.simple').toggle();
      $(this).find('.fa').toggleClass('fa-expand fa-compress');
    });

    controller.$element.on('click', controller.options.kindToggleSelector, function(e) {
        e.preventDefault();
        var $kindDisplay = controller.$element.find('.' + $(this).data('kind') + '-kind');
        $kindDisplay.toggle();
        var overflowContainer = controller.$element.parents('.oe-popup-overflow');
        if ($kindDisplay.is(':visible')) {
            // hide the show toggle
            controller.$element.find(controller.options.kindToggleSelector + '.show').hide();
            // in the summary popup, we want to scroll to the stopped drugs if they aren't visible
            if (overflowContainer.length) {
                OpenEyes.UI.Widgets.PatientSummaryPopup.addHeight($kindDisplay.height());
                if ($kindDisplay.position().top > overflowContainer.height()) {
                    overflowContainer.scrollTop($kindDisplay.position().top);
                }
            }
        } else {
            controller.$element.find(controller.options.kindToggleSelector + '.show').show();
        }

    });
  };

  exports.HistoryMedicationsViewController = HistoryMedicationsViewController;
})(OpenEyes.OphCiExamination);