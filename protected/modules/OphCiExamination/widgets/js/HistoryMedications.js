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
    this.$table = this.$element.find('#OEModule_OphCiExamination_models_HistoryMedications_entries');
    this.$popup = this.$element.find('#medication-history-popup');
    this.templateText = this.$element.find('.entry-template').text();
    this.drugsByRisk = {};
    this.medicationSearchRequest = null;
    this.initialiseFilters();
    this.initialiseTriggers();
    this.initialiseDatepicker();
  }

  HistoryMedicationsController._defaultOptions = {
    modelName: 'OEModule_OphCiExamination_models_HistoryMedications',
    element: undefined,
    addButtonSelector: '.js-add-select-search',
    popup:'#add-to-medication',
    removeButtonSelector: 'i.trash',
    searchSource: '/medication/finddrug',
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
    medicationDisplaySelector: '.medication-display',
    startDateButtonSelector: '.start-medication.enable',
    cancelStartDateButtonSelector: '.start-medication.cancel',
    stopDateFieldSelector: 'input[name$="[stop_date]"]',
    stopDateButtonSelector: '.stop-medication.enable',
    cancelStopDateButtonSelector: '.stop-medication.cancel',
    routeFieldSelector: 'select[name$="[route_id]"]',
    routeOptionWrapperSelector: '.admin-route-options'
  };

  /**
   * Setup datepicker
   */
  HistoryMedicationsController.prototype.initialiseDatepicker = function () {
    row_count = OpenEyes.Util.getNextDataKey( this.$element.find('table tbody tr'), 'key');
    for (var i=0; i < row_count; i++){
      this.constructDatepicker('#datepicker_1_'+i);
      this.constructDatepicker('#datepicker_2_'+i);
    }
  };

  HistoryMedicationsController.prototype.setDatepicker = function () {
    row_count = OpenEyes.Util.getNextDataKey( this.$element.find('table tbody tr'), 'key')-1;
    this.constructDatepicker('#datepicker_1_'+row_count);
    this.constructDatepicker('#datepicker_2_'+row_count);
  };

  HistoryMedicationsController.prototype.constructDatepicker = function (name) {
    var datepicker= $(this.$table).find(name);
    if (datepicker.length!=0){
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

      $row.on('change', '.fuzzy-date select', function(e) {
          var $fuzzyFieldset = $(this).closest('fieldset');
          var date = controller.dateFromFuzzyFieldSet($fuzzyFieldset);
          $fuzzyFieldset.find('input[type="hidden"]').val(date);
      });
      controller.setDatepicker();
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


  HistoryMedicationsController.prototype.loadDrugDefaults = function($row)
  {
      let drug_id = $row.find("input[name*='[drug_id]']").val();
      if(drug_id !== '') {
          $.getJSON('/medication/drugdefaults', {drug_id: drug_id}, function (res) {
              for (let name in res) {
                  let $input = $row.find('[name$="[' + name + ']"]');
                  if (name === 'dose') {
                      $input.attr('placeholder', res['dose_unit']);
                      $input.addClass('numbers-only');
                      if (res['dose_unit'] === 'mg') {
                          $input.addClass('decimal');
                      } else if (!res['dose_unit']) {
                          $input.removeClass('numbers-only decimal');
                      }

                      $input.val('');
                      $row.find('[name$="[units]"]').val(res['dose_unit']);
                  } else {
                      $input.val(res[name]).change();
                  }
              }
          });
      }
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

  HistoryMedicationsController.prototype.createRow = function(selectedItems)
  {

    var newRows = [];
    var template = this.templateText;
    var element = this.$element;

    for (var i in selectedItems) {
      data = {};
      data['row_count'] = OpenEyes.Util.getNextDataKey( element.find('table tbody tr'), 'key')+ newRows.length;
      if (selectedItems[i]['type'] == 'md'){
        data['medication_drug_id'] = selectedItems[i]['id'];
      } else {
        data['drug_id'] = selectedItems[i]['id'];
      }
      data['medication_name'] = selectedItems[i]['label'];
      this.processRisks(selectedItems[i]['tags'], selectedItems[i]['label']);
      newRows.push( Mustache.render(
        template,
        data ));
    }
    return newRows;

  };

  HistoryMedicationsController.prototype.addEntry = function(selectedItems)
  {
    var rows = this.createRow(selectedItems);
    for(var i in rows){
      this.$table.children('tbody').append(rows[i]);
      let $lastRow = this.$table.find('tbody tr:last');
      this.initialiseRow($lastRow);
      this.loadDrugDefaults($lastRow);
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

    /**
     * @TODO: should be common function across history elements
     * @param fieldset
     * @returns {*}
     */
    HistoryMedicationsController.prototype.dateFromFuzzyFieldSet = function(fieldset)
    {
        res = fieldset.find('select.fuzzy_year').val();
        var month = parseInt(fieldset.find('select.fuzzy_month option:selected').val());
        res += '-' + ((month < 10) ? '0' + month.toString() : month.toString());
        var day = parseInt(fieldset.find('select.fuzzy_day option:selected').val());
        res += '-' + ((day < 10) ? '0' + day.toString() : day.toString());

        return res;
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