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

    if(typeof window.switch_alternative !== "function") {
        window.switch_alternative = function(anchor) {
            var $wrapper = $(anchor).closest(".alternative-display-element");
            $wrapper.hide();
            $wrapper.siblings(".alternative-display-element").show();
            var $col = $wrapper.closest(".alternative-display");
            $col.next(".alt-display-trigger").hide();

            var $dropdown = $col.find(".js-unit-dropdown");
            var $input = $col.find(".dose_unit_term");

            if($dropdown.length > 0 && $input.val() == "") {
                $dropdown.removeAttr("disabled").show();
                $input.attr("disabled", "disabled");
            }
        };
    }

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
          'set_ids',
          'locked'
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
    patientAllergies: [],
      allAllergies: {},

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
    var $datepicker= $(this.$table).find(name);
    if ($datepicker.length > 0){
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
    } else {
    		this.$table.find('tr.originally-stopped').hide();
		}
  };

  HistoryMedicationsController.prototype.initialiseTriggers = function()
  {
    var controller = this;

    // removal button for table entries
    controller.$table.on('click', controller.options.removeButtonSelector, function(e) {
        e.preventDefault();
        var $row = $(e.target).closest("tr");
        var key = $row.attr("data-key");
        var $tapers = controller.$table.find("tr[data-parent-key="+key+"]");
        $tapers.remove();
        controller.removeBoundEntry($row);
        $row.remove();
    });

    // removal button for tapers
      controller.$table.on("click", ".js-remove-taper", function(e){
          e.preventDefault();
          var $row = $(e.target).closest("tr");
          $row.remove();
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

  HistoryMedicationsController.prototype.initialiseRow = function($row, data)
  {
      var controller = this;

      controller.updateTextualDisplay($row);
      if($row.find(".js-locked").val() === "1") {
          var $txt_display = $row.find(".dose-frequency-route .textual-display");
          $txt_display.replaceWith('<span class="textual-display">'+$txt_display.html()+'</span>');
          $row.find(".dose-frequency-route .alternative-display-element").hide();
          $row.find(".dose-frequency-route .alternative-display-element.textual").show();
      }

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
        var $input = $(this).closest(".toggle-switch").find("input");
        var checked = !$input.prop("checked");
        if(!checked) {
        		let $data_key = $row.attr('data-key');
						$(".js-taper-row[data-parent-key='" + $data_key + "']").remove();
            $row.find(".js-disppense-location option").empty();
            $row.find(".js-duration,.js-dispense-condition,.js-dispense-location").val("").hide();
            $row.find(".js-add-taper").hide();
        }
        else {
            $row.find(".js-duration,.js-dispense-condition,.js-dispense-location,.js-add-taper").show();
        }
      });

      var controls_onchange = function (e) {
          controller.updateBoundEntry($row);
          controller.updateTextualDisplay($row);
          if(typeof $row.data('bound_entry') !== 'undefined') {
              controller.updateTextualDisplay($row.data('bound_entry'));
              if($(e.target).hasClass("js-route")) {
                  controller.updateRowRouteOptions($row.data('bound_entry'));
              }
          }
      };

      controller.setDatepicker();

      $row.on("change", ".js-dose, .js-unit-dropdown, .js-frequency, .js-route, .js-laterality, .js-stop-reason, .js-start-date, .js-end-date", controls_onchange);
      var $end_date_ctrl = $row.find(".js-end-date");
      var $start_date_ctrl = $row.find(".js-start-date");

      if($end_date_ctrl.length > 0) {
          $end_date_ctrl[0].addEventListener('pickmeup-change', function(e){controls_onchange(e);});
      }

      if($start_date_ctrl.length > 0) {
          $start_date_ctrl[0].addEventListener('pickmeup-change', function(e){controls_onchange(e);});
      }

      $row.on("change", ".js-dispense-condition", function(){
          controller.getDispenseLocation($(this));
          return false;
      });

      $row.on("click", ".js-add-taper", function(){
          controller.addTaper($row);
          return false;
      });

      var med = $row.data("medication");
      if($row.find(".js-unit-dropdown").length > 0 && typeof med !== "undefined" &&
				(typeof med.dose_unit_term === "undefined" || med.dose_unit_term === "" ||
					(typeof data !== "undefined" && data.show_dose_units))
			) {
          	$row.find(".js-unit-dropdown").removeAttr("disabled").show();
						$row.find(".dose_unit_term").attr("disabled", "disabled");
						$row.find("span.js-dose-unit-term").hide();
      }
  };

  HistoryMedicationsController.prototype.getDispenseLocation = function($dispense_condition)
  {
      $.get("/OphDrPrescription/PrescriptionCommon/GetDispenseLocation", {
          condition_id: $dispense_condition.val(),
      }, function (data) {
          var $dispense_location = $dispense_condition.closest('tr').find('.js-dispense-location');
          $dispense_location.find('option').remove();
          $dispense_location.append(data);
          $dispense_location.show();
      });
  };

  HistoryMedicationsController.prototype.addTaper = function($row)
  {
      var row_count = $row.attr("data-key");
      var next_taper_count = 0;
      var last_taper_count;
      var controller = this;

      var $tapers = controller.$table.find("tr[data-parent-key="+row_count+"]");
      if($tapers.length > 0) {
          last_taper_count = parseInt($tapers.last().attr("data-taper-key"));
          next_taper_count = last_taper_count + 1;
      }

      var markup = Mustache.render(
          this.$element.find('.taper-template').text(),
          {
              'row_count' : row_count,
              'taper_count' : next_taper_count
          }
      );

      var $lastrow;

      if($tapers.length>0) {
          $lastrow = $tapers.last();
      }
      else {
          $lastrow = $row;
      }

      $(markup).insertAfter($lastrow);
  };

    HistoryMedicationsController.prototype.showStopControls = function($row)
    {
        var $datepicker_wrapper = $row.find(".js-end-date-wrapper");
        var $stop_reason_select = $row.find(".js-stop-reason");
        var $datepicker_control = $datepicker_wrapper.find("input");
        $row.find(".js-meds-stop-btn").hide();
        var default_date = $datepicker_control.attr("data-default");
        const currently_set_date = $datepicker_control.val();
        if(typeof default_date !== "undefined" && default_date !== false && !currently_set_date) {
            $datepicker_control.val(default_date);
        }
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
            var $element = $row.find("[name$='[entries]["+rc+"]["+field+"]']").not(":disabled");
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

    HistoryMedicationsController.prototype.getMergedAllergies = function ()
    {
        var controller = this;
       var patient_allergies = controller.options.patientAllergies;
       var merged_allergies = {};
       $.each(patient_allergies, function (i, e) {
            merged_allergies[e] = controller.options.allAllergies[e];
       });

       var $element = $("#OEModule_OphCiExamination_models_Allergies_element");

       if($element.length > 0) {
           var allergiesController = $element.data("controller");
           $.each(allergiesController.getAllergyIds(), function (i, e) {
               merged_allergies[e] = controller.options.allAllergies[e];
           });
       }

       return merged_allergies;
    };

    /**
     * Check if the selected entries are relevant to current allergies
     * Then add them depending on user choice
     *
     * @param selectedItems
     * @returns {boolean}
     */

    HistoryMedicationsController.prototype.addEntriesWithAllergyCheck = function(selectedItems)
    {
        var allergies = this.getMergedAllergies();
        var allergic_drugs = [];
        var controller = this;

        $.each(selectedItems, function (i, medication) {
            if(typeof medication.allergy_ids === "undefined" || medication.allergy_ids.toString() === "") {
                return;
            }

            var med_allergies = medication.allergy_ids.toString().split(",");
            var intersect = [];

            $.each(med_allergies, function (j, med_allergy) {
                for(var k in allergies) {
                    if(k == med_allergy) {
                        intersect.push(k);
                    }
                }
            });

            if(intersect.length > 0) {
                allergic_drugs.push(medication.label);
            }
        });

        if(allergic_drugs.length > 0) {

            var dialog = new OpenEyes.UI.Dialog.Confirm({
                content: "Patient is allergic to " +
                allergic_drugs.join() +
                ". Are you sure you want to add them?"
            });

            dialog.on('ok', function () {
                controller.addEntry(selectedItems);
            });

            dialog.on('cancel' , function(){
                var items_to_add = [];

                $.each(selectedItems, function(i,e) {
                    if(allergic_drugs.indexOf(e.label) === -1) {
                        items_to_add.push(e);
                    }
                });

                controller.addEntry(items_to_add);
            });

            dialog.open();
        }
        else {
            controller.addEntry(selectedItems);
        }

        return true;
    };

    HistoryMedicationsController.prototype.updateTextualDisplay = function ($row) {
    		let displayDoseText = "";
    		if($row.find(".js-dose").val() !== '') {
					displayDoseText = $row.find(".js-dose").val() + " " + $row.find(".js-dose-unit-term").text()
				}
        $row.find(".js-textual-display-dose").text(displayDoseText);
        $row.find(".js-textual-display-frequency").text($row.find(".js-frequency option:selected").text());
        var route_lat = "";
        var $lat_ctrl = $row.find(".admin-route-options");
        if($lat_ctrl.val() !== "") {
            route_lat = $lat_ctrl.find("option:selected").text()+" ";
        }
        route_lat+=$row.find(".js-route option:selected").text();
        $row.find(".js-textual-display-route-laterality").text(route_lat);
    };

    HistoryMedicationsController.prototype.updateAllergyStatuses = function(new_allergy_ids)
    {
        var allergies = this.getMergedAllergies();
        var controller = this;
        var matched_allergy_ids = [];

        var match_allergies = function($row, allergy_ids) {
            var intersection = [];
            $.each(allergy_ids, function(i, id) {
                for(var j in allergies) {
                    if(id == j)  {
                        intersection.push(id);
                        matched_allergy_ids.push(parseInt(id));
                    }
                }
            });

            $row.find(".js-allergy-warning").remove();

            if(intersection.length > 0) {
                var allergy_warning = controller.getAllergyWarningForAllergyIds(intersection);
                $(allergy_warning).prependTo($row.find(".js-prepended_markup"));
                return true;
            }
            else {
                return false;
            }
        };

        $.each(this.$table.find("tbody > tr"), function (i, row) {
            var allergy_ids = $(row).attr("data-allergy-ids");
            var allergies = [];
            if(typeof allergy_ids !== "undefined" && allergy_ids !== "") {
                allergies = allergy_ids.split(",");
            }

            if(allergies.length > 0) {
                match_allergies($(row), allergies);
            }
        });

        /* If there are new allergy matches, trigger an alert */

        if(typeof new_allergy_ids !== "undefined") {
            let intersection = matched_allergy_ids.filter(x => new_allergy_ids.includes(x));

            if(intersection.length > 0) {
                let dlg = new OpenEyes.UI.Dialog.Alert({
                    content: "Allergy warning! Please check entries in Medication Management."
                });
                dlg.open();
            }
        }
    };

    HistoryMedicationsController.prototype.bindController = function(controller, name) {
        this[name] = controller;
        this.boundController = controller;
    };

    HistoryMedicationsController.prototype.disableRemoveButton = function ($row) {
        const $removeButton = $row.find(".js-remove");
        const $removeButtonWrapper = $removeButton.parent();
        $removeButton.addClass("disabled");
        $removeButtonWrapper.addClass("js-has-tooltip");
        $removeButtonWrapper.data("tooltip-content", $removeButtonWrapper.data("tooltip-content-comes-from-history"));
    };

    HistoryMedicationsController.prototype.copyRow = function($origin, $target, old_values)
    {
        var $row = $(this.boundController.createRow());
        $row.appendTo($target);
        var data = this.getRowData($origin, old_values);
        data.show_dose_units = !($origin.find('select.js-unit-dropdown').attr('disabled') === "disabled");
        data.usage_type = $target.attr("data-usage-type");

        /*
        when a drug that comes from history is missing the values that are required for prescription
        the row should be editable
        currently required fields: 'dose, route_id, frequency_id, dose_unit_term'
         */
        if(data.dose != "" && data.route_id != "" && data.frequency_id != "" && data.dose_unit_term != "") {
            data.locked = 1;
        }

        this.boundController.setRowData($row, data);
        this.boundController.initialiseRow($row, data);
        if(data.end_date !== "") {
            this.showStopControls($row);
        }
        this.updateRowRouteOptions($row);

        $row.find(".js-prepended_markup:visible").load("/medicationManagement/getInfoBox?medication_id="+data.medication_id);
        this.disableRemoveButton($row);

        return $row;
    };

    HistoryMedicationsController.prototype.getRandomBindedKey = function() {
			let uniqueKeyFound = false;
			let randomKey;
			while(!uniqueKeyFound) {
				randomKey = generateId();
				uniqueKeyFound = true;
				$.each($(window).find('.js-binded-key'), function(index, $bindedKey){
					if(randomKey === $bindedKey.val()){
						uniqueKeyFound = false;
					}
				});
			}

			return randomKey;
		};

	  HistoryMedicationsController.prototype.bindEntries = function ($row1, $row2, generateRandomKey) {
	  	if (generateRandomKey === undefined) {
	  		generateRandomKey = true;
	  	}

	  	if (generateRandomKey) {
	  		let randomBindedKey = this.getRandomBindedKey();

	  		$row1.find('.js-binded-key').val(randomBindedKey);
	  		$row2.find('.js-binded-key').val(randomBindedKey);
	  	}
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
        //controller.updateRowRouteOptions($bound_entry);

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
   * @param setIds
   * @param drug_name
   */
  HistoryMedicationsController.prototype.processRisks = function(setIds , drug_name)
  {
      if (typeof setIds === "undefined" || setIds.length === 0 || (setIds.length === 1 && setIds[0] === "")) {
          return;
      }
      var self = this;
      $.getJSON('/OphCiExamination/Risks/forSets', { set_ids: setIds }, function (res) {
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
      var risksMap = [];
      for (var i=0; i < risks.length; i++) {
          risksMap.push({id: risks[i].id, comments: [drugName], risk_name: risks[i].name});
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

                  if(typeof $row.data("medication") !== "undefined" && $row.data("medication").laterality !== "") {
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
      this.processRisks(medications[i]['set_ids'.split(",")], medications[i]['medication_name']);
      data['allergy_warning'] = this.getAllergyWarning(medications[i]);
      newRows.push(Mustache.render(
          template,
          data ));
    }

    return newRows;
  };

    HistoryMedicationsController.prototype.getAllergyWarning = function(medication)
    {
        if(typeof medication.allergy_ids === "undefined" || medication.allergy_ids.toString() === "") {
            return "";
        }

        var patient_allergies = this.getMergedAllergies();
        var med_allergies = medication.allergy_ids.toString().split(",");
        var intersect = [];
        var allergy_names = [];
        var controller = this;

        $.each(med_allergies, function (j, med_allergy) {
            for(var k in patient_allergies) {
                if(k == med_allergy) {
                    intersect.push(k);
                }
            }
        });

        if(intersect.length > 0) {
            $.each(intersect, function (i, e) {
                allergy_names.push(controller.options.allAllergies[e]);
            });
            return '<i class="oe-i warning small pad js-has-tooltip js-allergy-warning" data-tooltip-content="Allergic to ' + allergy_names.join(", ") + '"></i>';
        }
        else {
            return "";
        }
    };

    HistoryMedicationsController.prototype.getAllergyWarningForAllergyIds = function(allergy_ids)
    {
        var allergy_names = [];
        var controller = this;

        $.each(allergy_ids, function (i, e) {
            allergy_names.push(controller.options.allAllergies[e]);
        });

        return '<i class="oe-i warning small pad js-has-tooltip js-allergy-warning" data-tooltip-content="Allergic to ' + allergy_names.join(", ") + '"></i>';
    };

    HistoryMedicationsController.prototype.addEntry = function (selectedItems, do_callback)
    {
        var medication = [];

        $.each(selectedItems, function (i, e) {

            if(typeof selectedItems[i].label !== "undefined") {
                // added from Adder Dialog
                medication[i] = {
                    medication_id: selectedItems[i].id,
                    default_form: selectedItems[i].default_form,
                    dose: selectedItems[i].dose,
                    dose_unit_term: selectedItems[i].dose_unit_term,
                    medication_name: selectedItems[i].label,
                    route_id: selectedItems[i].route_id,
                    frequency_id: selectedItems[i].frequency_id,
                    will_copy: selectedItems[i].will_copy,
                    to_be_copied: selectedItems[i].will_copy,
                    prepended_markup: selectedItems[i].prepended_markup,
                    set_ids: selectedItems[i].set_ids,
                    allergy_ids: selectedItems[i].allergy_ids,
                };
            }
            else {
                // added as a copy of another row
                medication[i] = JSON.parse(JSON.stringify(selectedItems[i]));
            }

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

            if(do_callback) {
                this.options.onAddedEntry($lastRow, this);
            }
        }

        $(this.options.medicationSelectOptions).find('.selected').removeClass('selected');
        $(this.options.medicationSearchInput).val('');
        $(this.options.medicationSearchResult).empty();

        // return the last created row
        return $newrow;
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