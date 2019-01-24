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
    this.$table = this.$element.find('.js-entry-table');
    this.$popup = this.$element.find('#medication-history-popup');
    this.templateText = this.$element.find('.entry-template').text();
    this.drugsByRisk = {};
    this.medicationSearchRequest = null;
      this.fields = [
          'medication_name',
          'medication_id',
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
          'prescription_item_id',
          'to_be_copied',
          'prepended_markup',
      ];

    this.initialiseFilters();
    this.initialiseTriggers();
    this.initialiseDatepicker();

      this.$element.data("controller_instance", this);
      this.options.onInit(this);
  }

  HistoryMedicationsController._defaultOptions = {
    modelName: 'OEModule_OphCiExamination_models_HistoryMedications',
    element: undefined,
    addButtonSelector: '.js-add-select-search',
    popup:'#add-to-medication',
    removeButtonSelector: 'i.js-remove',
    searchSource: '/medicationManagement/findRefMedications',
    routeOptionSource: '/medication/retrieveDrugRouteOptions',
    searchAsTypedPrefix: 'As typed: ',
    drugFieldSelector: 'input[name$="[drug_id]"]',
    medicationFieldSelector: 'input[name$="[medication_drug_id]"]',
    asTypedFieldSelector: 'input[name$="[medication_name]"]',
    medicationSelectOptions:'#history-medication-select-options',
    medicationSearchOptions: '.history-medication-search-options',
    medicationSearchInput: '#history-medication-search-field',
    medicationSearchResult: '#history-medication-search-results',
    medicationNameSelector: '.medication-name',
    medicationDisplaySelector: '.js-medication-display',
    startDateButtonSelector: '.start-medication.enable',
    cancelStartDateButtonSelector: '.start-medication.cancel',
    stopDateFieldSelector: 'input[name$="[stop_date]"]',
    stopDateButtonSelector: '.stop-medication.enable',
    cancelStopDateButtonSelector: '.stop-medication.cancel',
    routeFieldSelector: 'select[name$="[route_id]"]',
    routeOptionWrapperSelector: '.admin-route-options',

      // Customizable callbacks

      onInit: function(){},
      onControllerBound: function (controller, name){},
      onAddedEntry: function($row, controller){},
      onRemovedEntry: function ($row, controller) {}
  };

  /**
   * Setup datepicker
   */
  HistoryMedicationsController.prototype.initialiseDatepicker = function () {
      var modelName = this.options['modelName'];
    var row_count = OpenEyes.Util.getNextDataKey( this.$element.find('table tbody tr'), 'key');
    for (var i=0; i < row_count; i++){
      this.constructDatepicker('#'+modelName+'_datepicker_2_'+i);
      this.constructDatepicker('#'+modelName+'_datepicker_3_'+i);
    }
  };

  HistoryMedicationsController.prototype.setDatepicker = function () {
      var modelName = this.options['modelName'];
    var row_count = OpenEyes.Util.getNextDataKey( this.$element.find('table tbody tr'), 'key')-1;
    this.constructDatepicker('#'+modelName+'_datepicker_2_'+row_count);
    this.constructDatepicker('#'+modelName+'_datepicker_3_'+row_count);
  };

  HistoryMedicationsController.prototype.constructDatepicker = function (name) {
    var datepicker= $(this.$table).find(name);
    if (datepicker.length > 0){
      pickmeup(name, {
        format: 'Y-m-d',
        hide_on_select: true,
        default_date: false
      });
    }
  };

  /**
  * Sets up the filter controls on the table.
  */
  HistoryMedicationsController.prototype.initialiseFilters = function()
  {
    // if there aren't any stopped medications, then the filter is irrelevant
    if (!this.$table.find('tr.originally-stopped').length) {
        this.$element.find('.show-stopped').hide();
        this.$element.find('.hide-stopped').hide();
    }
  };

  HistoryMedicationsController.prototype.initialiseTriggers = function()
  {
    var controller = this;

    // removal button for table entries
    controller.$table.on('click', controller.options.removeButtonSelector, function(e) {
      e.preventDefault();
      $(e.target).parents('tr').remove();
    });

    // setup current table row behaviours
    controller.$table.find('tbody tr').each(function() {
      controller.initialiseRow($(this));
    });

    // adding entries
    controller.$popup.on('click', controller.options.addButtonSelector, function(e) {
      e.preventDefault();
      controller.$table.find('thead').show();
      controller.addEntry();
    });

    controller.$element.on('click', '.show-stopped', function(e) {
        e.preventDefault();
        controller.showStopped();
    });

    controller.$element.on('click', '.hide-stopped', function(e) {
        e.preventDefault();
        controller.hideStopped();
    });
    controller.$element.on('click','#history-medication-search-btn',  function (e) {
      if ($(this).hasClass('selected')) {
        return;
      }

      $(this).addClass('selected');
      $('#history-medication-select-btn').removeClass('selected');

      $(controller.options.medicationSearchOptions).show();
      $(controller.options.medicationSelectOptions).find('.selected').removeClass('selected');
      $(controller.options.medicationSelectOptions).hide();
    });

    controller.$element.on('click','#history-medication-select-btn', function () {
      if ($(this).hasClass('selected')) {
        return;
      }

      $(this).addClass('selected');
      $('#history-medication-search-btn').removeClass('selected');

      $(controller.options.medicationSelectOptions).show();
      $(controller.options.medicationSearchOptions).hide();
      $(controller.options.medicationSearchInput).val('');
      $(controller.options.medicationSearchResult).empty();
    });

    $(controller.options.medicationSearchInput).on('keyup', function () {
      controller.popupSearch();
    });
  };

  HistoryMedicationsController.prototype.initialiseRow = function($row)
  {
      var controller = this;

      $row.on('click', '.medication-rename', function(e) {
          e.preventDefault();
          controller.resetSearchRow($row, true);
      });

      $row.on('change', controller.options.routeFieldSelector, function(e) {
          controller.updateRowRouteOptions($row);
      });

      $row.on("click", ".js-meds-stop-btn", function(){
          controller.showStopControls($row);
      });

      $row.on("click", ".js-btn-prescribe", function () {
        var $btn = $(this);
        var $input = $btn.find("input");
        var $icon = $btn.find("i");

        if($input.val() == "1") {
            $input.val(0);
            $icon.css("opacity", "");
        }
        else {
            $input.val(1);
            $icon.css("opacity", 1);
        }
      });

      var controls_onchange = function (e) {
          controller.updateBoundEntry($row);
          if($(e.target).hasClass("route") && typeof $row.data('bound_entry') !== 'undefined') {
              controller.updateRowRouteOptions($row.data('bound_entry'));
          }
      };

      controller.setDatepicker();

      $row.on("change", ".js-dose, .js-frequency, .js-route, .js-laterality, .js-stop-reason", controls_onchange);
      var $end_date_ctrl = $row.find(".js-end-date");
      var $start_date_ctrl = $row.find(".js-start-date");

      if($end_date_ctrl.length > 0) {
          $end_date_ctrl[0].addEventListener('pickmeup-change', function(e){controls_onchange(e);});
      }

      if($start_date_ctrl.length > 0) {
          $start_date_ctrl[0].addEventListener('pickmeup-change', function(e){controls_onchange(e);});
      }

        //  controller.updateRowRouteOptions($row);
  };

    HistoryMedicationsController.prototype.showStopControls = function($row)
    {
        var $datepicker_wrapper = $row.find(".js-end-date-wrapper");
        var $stop_reason_select = $row.find(".js-stop-reason");
        $row.find(".js-meds-stop-btn").hide();
        $datepicker_wrapper.show();
        $stop_reason_select.show();

        if(typeof $row.data("bound_entry") !== "undefined") {
            this.boundController.showStopControls($row.data("bound_entry"));
        }
    };

  HistoryMedicationsController.prototype.popupSearch = function()
  {
    var controller = this;
    if (controller.medicationSearchRequest !== null) {
      controller.medicationSearchRequest.abort();
    }

    controller.medicationSearchRequest = $.getJSON(controller.options.searchSource, {
      term: $(controller.options.medicationSearchInput).val(),
      ajax: 'ajax'
    }, function (ui) {
      controller.medicationSearchRequest = null;
      $(controller.options.medicationSearchResult).empty();
      var no_data = !$(ui).length;
      $(controller.options.medicationSearchResult).toggle(!no_data);
      $('#history-medication-search-no-results').toggle(no_data);
      for (let i = 0 ; i < ui.length ; i++ ){
        var span = "<span class='auto-width'>"+ui[i]['name']+"</span>";
        var item = $("<li>")
          .attr('data-str', ui[i]['name'])
          .attr('data-medication-drug-id', ui[i]['value']);
        item.append(span);
        $(controller.options.medicationSearchResult).append(item);
      }
    });

  };

    HistoryMedicationsController.prototype.loadDrugDefaults = function($row, medication)
    {
        $row.find(".js-dose").val(medication.dose);
        $row.find(".js-dose-unit-term").text(medication.dose_unit_term);
        $row.find(".js-route").val(medication.route_id);
        this.updateRowRouteOptions($row);
    };

    HistoryMedicationsController.prototype.getRowsData = function () {
        var data = [];
        var self = this;
        $.each(this.$table.find("tbody").find("tr"), function (i, e) {
            data.push(self.getRowData($(e)));
        });
        return data;
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
            var elementval = $element.val();
            if(typeof old_values !== "undefined" && old_values) {
                var oldval = $element.attr("data-oldvalue");
                obj[field] = typeof oldval !== "undefined" ? oldval : elementval;
            }
            else {
                obj[field] =  elementval;
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
        $row.find(".js-medication-display").text(data.medication_name);
        $row.find(".js-dose-unit-term").text(data.dose_unit_term);

        $row.data("medication", data);

    };

    HistoryMedicationsController.prototype.bindController = function(controller, name) {
        this[name] = controller;
        this.boundController = controller;
        this.options.onControllerBound(controller, name);
    };


    HistoryMedicationsController.prototype.copyRow = function($origin, $target, old_values)
    {
        var $row = $(this.boundController.createRow());
        $row.appendTo($target);
        var data = this.getRowData($origin, old_values);
        data.usage_type = $target.attr("data-usage-type");

        this.boundController.initialiseRow($row);
        this.boundController.setRowData($row, data);

        $row.find(".js-prepended_markup:visible").load("/medicationManagement/getInfoBox?medication_id="+data.medication_id);

        return $row;
    };

    HistoryMedicationsController.prototype.bindEntries = function($row1, $row2)
    {
        $row1.data("bound_entry", $row2);
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
        controller.updateRowRouteOptions($bound_entry);

        if(data.end_date !== "") {
            controller.showStopControls($bound_entry);
        }

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


    HistoryMedicationsController.prototype.setDoNotSaveEntries = function(v)
    {
        this.$element.find(".js-do-not-save-entries").val(v ? "1" : "0");
    };


    /**
   * From the tags on the given item, retrieve the associated risks and update the core
   * register accordingly.
   *
   * @param item
   */
  HistoryMedicationsController.prototype.processRisks = function(tagIds , drug_name)
  {
      if (!tagIds) {
          return;
      }
      var self = this;
      $.getJSON('/OphCiExamination/Risks/forTags', { tag_ids: tagIds }, function (res) {
          self.addDrugForRisks(drug_name, res);
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
      risksMap = [];
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
      if (value !== "" && typeof value !== "undefined") {
          $.getJSON(this.options.routeOptionSource, {route_id: value}, function(data) {
              if (data.length) {
                  var $select = $routeOptionWrapper;
                  $.each(data, function(i, item) {
                    $select.append('<option value="' + item.id +'">' + item.name + '</option>');
                  });

                  if($row.data("medication").laterality !== "") {
                      $select.val($row.data("medication").laterality);
                  }

                  $routeOptionWrapper.show();
              }
          });
      }
  };

  HistoryMedicationsController.prototype.createRow = function(medications)
  {
      var newRows = [];
      var template = this.templateText;
      var element = this.$element;
      var data = {};

      if(typeof medications === "undefined") {
          // just create an empty row
          data.row_count = OpenEyes.Util.getNextDataKey( this.$element.find('table tbody tr'), 'key');
          return Mustache.render(
              this.templateText,
              data
          );
      }

    for (var i in medications) {
      data = medications[i];
      data['row_count'] = OpenEyes.Util.getNextDataKey( element.find('table tbody tr'), 'key')+ newRows.length;
      this.processRisks(medications[i]['tags'], medications[i]['medication_name']);
      newRows.push(Mustache.render(
          template,
          data ));
    }

    return newRows;

  };

    HistoryMedicationsController.prototype.addEntry = function (selectedItems)
    {
        var medication = [];

        $.each(selectedItems, function (i, e) {

            console.log(selectedItems[i]);

            medication[i] = {
                medication_id: selectedItems[i].id,
                default_form: selectedItems[i].default_form,
                dose: selectedItems[i].dose,
                dose_unit_term: selectedItems[i].dose_unit_term,
                medication_name: selectedItems[i].label,
                route_id: selectedItems[i].route,
                frequency_id: selectedItems[i].frequency,
                will_copy: selectedItems[i].will_copy,
                to_be_copied: selectedItems[i].will_copy,
                prepended_markup: selectedItems[i].prepended_markup
            };
        });

        var rows = this.createRow(medication);
        var $newrow;
        for (var i in rows) {

            $newrow = $(rows[i]);

            $newrow.appendTo(this.$table.children('tbody'));
            this.setRowData($newrow, medication[i]);
            let $lastRow = this.$table.find('tbody tr:last');

            this.initialiseRow($lastRow);
            this.loadDrugDefaults($lastRow, medication[i]);

            if (medication[i].will_copy && typeof this.boundController !== "undefined") {
                var $copy = this.copyRow($lastRow, this.boundController.$table.children("tbody"));
                this.bindEntries($lastRow, $copy);
            }
        }

        $(this.options.medicationSelectOptions).find('.selected').removeClass('selected');
        $(this.options.medicationSearchInput).val('');
        $(this.options.medicationSearchResult).empty();
    };

  HistoryMedicationsController.prototype.getItemDisplayValue = function(item)
    {
        if (item.type === 't') {
            return item.label.replace(this.options.searchAsTypedPrefix, '');
        }
        return item.label;
    };

    HistoryMedicationsController.prototype.showStopped = function()
    {
        this.$table.find('tr.originally-stopped').show();
        this.$element.find('.show-stopped').hide();
        this.$element.find('.hide-stopped').show();
    };

    HistoryMedicationsController.prototype.hideStopped = function()
    {
        this.$table.find('tr.originally-stopped').hide();
        this.$element.find('.show-stopped').show();
        this.$element.find('.hide-stopped').hide();
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