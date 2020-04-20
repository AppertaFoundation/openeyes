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
            let $wrapper = $(anchor).closest(".alternative-display-element");
            $wrapper.hide();
            $wrapper.siblings(".alternative-display-element").show();
            let $col = $wrapper.closest(".alternative-display");
            $col.next(".alt-display-trigger").hide();

            let $dropdown = $col.find(".js-unit-dropdown");
            let $input = $col.find(".dose_unit_term");

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
    this.$currentMedicationsTable = this.$element.find('.js-entry-table.js-current-medications');
    this.$popup = this.$element.find('#medication-history-popup');
    this.templateText = this.$element.find('.entry-template').text();
    this.taperTemplateText = this.$element.find('.taper-template').text();
    this.drugsByRisk = {};
    this.medicationSearchRequest = null;
      this.fields = [
          'medication_name',
          'medication_id',
          'dose',
          'dose_unit_term',
          'frequency_id',
          'duration_id',
          'route_id',
          'laterality',
          'start_date',
          'end_date',
          'stop_reason_id',
          'usage_type',
          'group',
          'hidden',
          'prescription_item_id',
          'dispense_condition_id',
          'to_be_copied',
          'prepended_markup',
          'set_ids',
          'locked',
          'bound_key'
      ];

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
        drugSetFormSource: '/medicationManagement/getDrugSetForm',
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
        routeOptionInputSelector: '.laterality-input',
        patientAllergies: [],
          allAllergies: {},
          classes_that_dont_break_binding: ['js-end-date', 'js-stop-reason'],

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
      let modelName = this.options['modelName'];
    let row_count = OpenEyes.Util.getNextDataKey( this.$element.find('table tbody tr'), 'key');
    for (let i=0; i < row_count; i++){
      this.constructDatepicker('#'+modelName+'_entries_'+i+'_start_date');
      this.constructDatepicker('#'+modelName+'_entries_'+i+'_end_date');
    }
  };

  HistoryMedicationsController.prototype.setDatepicker = function () {
      let modelName = this.options['modelName'];
    let row_count = OpenEyes.Util.getNextDataKey( this.$element.find('table tbody tr'), 'key')-1;
		this.constructDatepicker('#'+modelName+'_entries_'+row_count+'_start_date');
		this.constructDatepicker('#'+modelName+'_entries_'+row_count+'_end_date');
  };

  HistoryMedicationsController.prototype.constructDatepicker = function (name) {
    let $datepicker= $(this.$table).find(name);
    if ($datepicker.length > 0){
      pickmeup(name, {
        format: 'Y-m-d',
        hide_on_select: true,
        default_date: false
      });
    }
  };

  HistoryMedicationsController.prototype.initialiseTriggers = function()
  {
    let controller = this;

    // removal button for table entries
    controller.$table.on('click', controller.options.removeButtonSelector, function(e) {
        e.preventDefault();
        let $row = $(e.target).closest("tr");
        let $closest_table = $(e.target).closest('table');

        let key = $row.attr("data-key");
        let $tapers = controller.$table.find("tr[data-parent-key="+key+"]");
        $tapers.remove();
        controller.removeBoundEntry($row);
        controller.$table.find('tr[data-key=' + key + ']').remove();
        controller.displayTableHeader();

			if($closest_table.hasClass("js-stopped-medications")) {
				$closest_table.closest('.collapse-data').find('.js-stopped-medications-count').text('(' +$closest_table.find('tr.js-first-row').length + ')');
			}
    });

    // removal button for tapers
      controller.$table.on("click", ".js-remove-taper", function(e){
          e.preventDefault();
          let $row = $(e.target).closest("tr");
          $row.remove();
      });

    // setup current table row behaviours
    controller.$table.find('tbody tr.js-first-row').each(function() {
      controller.initialiseRowEventTriggers($(this));
    });

    // adding entries
    controller.$popup.on('click', controller.options.addButtonSelector, function(e) {
      e.preventDefault();
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

  HistoryMedicationsController.prototype.initialiseRowEventTriggers = function($row, data)
  {
      let controller = this;
      let key = $row.data('key');
      let $full_row = controller.$table.find('tr[data-key=' + key + ']');
      let $second_part_of_row = controller.$table.find('tr[data-key=' + key + '].js-second-row');
      controller.updateTextualDisplay($row);
      if($full_row.find(".js-locked").val() === "1") {
          let $txt_display = $full_row.find(".dose-frequency-route .textual-display");
          $txt_display.replaceWith('<span class="textual-display">'+$txt_display.html()+'</span>');
				$full_row.find(".dose-frequency-route .alternative-display-element").hide();
				$full_row.find(".dose-frequency-route .alternative-display-element.textual").show();
      }

		$full_row.on('change', controller.options.routeFieldSelector, function(e) {
          controller.updateRowRouteOptions($row);
      });

		$second_part_of_row.on("click", ".js-meds-stop-btn", function(){
          controller.showStopControls($full_row);
      });

      $second_part_of_row.on("click", ".js-start-date-display", function(){
          $(this).hide();
          $full_row.find(".js-start-date-wrapper").show();
      });

		$full_row.on("click", ".js-btn-prescribe", function () {
            let $input = $(this).closest(".toggle-switch").find("input");
            let checked = !$input.prop("checked");
            if(!checked) {
                let $data_key = $row.attr('data-key');
                $(".js-taper-row[data-parent-key='" + $data_key + "']").remove();
                $full_row.find(".js-dispense-location option").empty();
                $full_row.find(".js-duration,.js-dispense-condition,.js-dispense-location").val("").hide();
                $full_row.find(".js-add-taper").hide();
                $second_part_of_row.find('.end-date-column, .js-meds-stop-btn').show();
            }
            else {
                $full_row.find(".js-duration,.js-dispense-condition,.js-dispense-location,.js-add-taper").show();
                $second_part_of_row.find('.js-end-date, .js-stop-reason').val('');
                $second_part_of_row.find(".js-end-date-wrapper, .js-stop-reason-select, .js-stop-reason-text, .js-meds-stop-btn, .end-date-column").hide();
            }
      });

		$full_row.on("change",controller.options.routeOptionWrapperSelector, function() {
			let input_value = 0;
			$(this).find('input').each(function (){
					if($(this).is(':checked')) {
						input_value += parseInt($(this).val());
					}
			});
			$full_row.find(controller.options.routeOptionInputSelector).val(input_value);
		});

      let controls_onchange = function (e) {
          let $bound_entry = $row.data('bound_entry');
          let bound_entry_key = $bound_entry.data('key');
					let $full_bound_entry;
					if (typeof $bound_entry !== 'undefined') {
						$full_bound_entry = $bound_entry.parent().find("tr[data-key=" + bound_entry_key + "]");
					}

          if (controller.options['modelName'] === "OEModule_OphCiExamination_models_HistoryMedications") {
              controller.updateBoundEntry($full_row);
              controller.updateTextualDisplay($full_row);
              if (typeof $bound_entry !== 'undefined') {
                  controller.updateTextualDisplay($full_bound_entry);
                  if ($(e.target).hasClass('js-route')) {
                      controller.updateRowRouteOptions($full_bound_entry);
                  }
              }
          } else {
              if (typeof $bound_entry !== 'undefined') {
                  let row_needs_bond_removed = true;

                  controller.options.classes_that_dont_break_binding.forEach(function (class_name) {
                      if ($(e.target).hasClass(class_name)) {
                          row_needs_bond_removed = false;
                          $full_bound_entry.find('.' + class_name).attr('value', $full_row.find('.' + class_name).attr('value'));
                          if (class_name === 'js-end-date') {
                              let date_display = OpenEyes.Util.formatDateToDisplayString(new Date($full_row.find('.' + class_name).attr('value')));
                              $full_bound_entry.find('.alternative-display-element.textual:not(.flex-meds-inputs)').children().html(
                                  '<i class="oe-i stop small pad"></i>' + date_display
                              );
                          } else if (class_name === 'js-stop-reason') {
                              $full_bound_entry.find('.js-stop-reason-text').html($full_row.find('.' + class_name + ' option:selected').html());
                          }
                      }
                  });

                  if (row_needs_bond_removed) {
                      $bound_entry.removeData('bound_entry');
                      controller.setBoundEntryStop($bound_entry);
                      $row.find('.js-bound-key').val(controller.getRandomBoundKey());
                      $row.removeData('bound_entry');
                      controller.enableManualRowDeletion($row);
                  }
              }
          }
      };

      controller.setDatepicker();

		$full_row.on("change", ".js-dose, .js-unit-dropdown, .js-frequency, .js-route, .js-laterality, .js-stop-reason, .js-start-date, .js-end-date", controls_onchange);
      let $end_date_ctrl = $full_row.find(".js-end-date");
      let $start_date_ctrl = $full_row.find(".js-start-date");

      if($end_date_ctrl.length > 0) {
          $end_date_ctrl[0].addEventListener('pickmeup-change', function(e){controls_onchange(e);});
      }

      if($start_date_ctrl.length > 0) {
          $start_date_ctrl[0].addEventListener('pickmeup-change', function(e){controls_onchange(e);});
      }

		$full_row.on("change", ".js-dispense-condition", function(){
          controller.getDispenseLocation($(this), null);
          return false;
      });

		$full_row.on("click", ".js-add-taper", function(){
          controller.addTaper($second_part_of_row);
          return false;
      });

      let med = $row.data("medication");
      if($row.find(".js-unit-dropdown").length > 0 && typeof med !== "undefined" &&
				(typeof med.dose_unit_term === "undefined" || med.dose_unit_term === "" ||
					(typeof data !== "undefined" && data.show_dose_units))
			) {
          	$row.find(".js-unit-dropdown").removeAttr("disabled").show();
						$row.find(".dose_unit_term").attr("disabled", "disabled");
						$row.find("span.js-dose-unit-term").hide();
      }
  };

  HistoryMedicationsController.prototype.getDispenseLocation = function($dispense_condition, dispense_location)
  {
      $.get("/OphDrPrescription/PrescriptionCommon/GetDispenseLocation", {
          condition_id: $dispense_condition.val(),
      }, function (data) {
          let $dispense_location = $dispense_condition.closest('tr').find('.js-dispense-location');
          $dispense_location.find('option').remove();
          $dispense_location.append(data);
          $dispense_location.show();
          if (dispense_location) {
              $dispense_location.val(dispense_location);
          }
      });
  };

  HistoryMedicationsController.prototype.addTaper = function($row)
  {
      let row_count = $row.attr("data-key");
      let next_taper_count = 0;
      let last_taper_count;
      let controller = this;

      let $tapers = controller.$table.find("tr[data-parent-key="+row_count+"]");
      if($tapers.length > 0) {
          last_taper_count = parseInt($tapers.last().attr("data-taper-key"));
          next_taper_count = last_taper_count + 1;
      }

      let markup = Mustache.render(
          this.$element.find('.taper-template').text(),
          {
              'row_count' : row_count,
              'taper_count' : next_taper_count
          }
      );

      let $lastrow;

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
        let $datepicker_wrapper = $row.find(".js-end-date-wrapper");
			  let $stop_reason_select = $row.find(".js-stop-reason-select");
			  let $stop_reason_text = $row.find(".js-stop-reason-text");
			  let $datepicker_control = $datepicker_wrapper.find("input");
        $row.find(".js-meds-stop-btn").hide();
			  let default_date = $datepicker_control.attr("data-default");
        const currently_set_date = $datepicker_control.val();
        if(typeof default_date !== "undefined" && default_date !== false && !currently_set_date) {
            $datepicker_control.val(default_date);
        }
        $datepicker_wrapper.show();
        $stop_reason_select.show();
				$stop_reason_text.hide();

        if(typeof $row.data("bound_entry") !== "undefined" && $row.data("bound_entry").find('.js-meds-stop-btn').attr('style') !== "display: none;") {
					let $bound_entry = $row.data("bound_entry");

            this.boundController.showStopControls($bound_entry.parent().find('tr[data-key=' + $bound_entry.data('key') + '].js-second-row'));
        }
    };

  HistoryMedicationsController.prototype.popupSearch = function()
  {
    let controller = this;
    if (controller.medicationSearchRequest !== null) {
      controller.medicationSearchRequest.abort();
    }

    controller.medicationSearchRequest = $.getJSON(controller.options.searchSource, {
      term: $(controller.options.medicationSearchInput).val(),
      ajax: 'ajax'
    }, function (ui) {
      controller.medicationSearchRequest = null;
      $(controller.options.medicationSearchResult).empty();
      let no_data = !$(ui).length;
      $(controller.options.medicationSearchResult).toggle(!no_data);
      $('#history-medication-search-no-results').toggle(no_data);
      for (let i = 0 ; i < ui.length ; i++ ){
        let span = "<span class='auto-width'>"+ui[i]['name']+"</span>";
        let item = $("<li>")
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
        let data = [];
        let self = this;
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
        let rc = $row.attr("data-key");
        let obj = {};
        $.each(this.fields, function(i, field){
            let $element = $row.find("[name$='[entries]["+rc+"]["+field+"]']").not(":disabled");
            let elementval = $element.val();
            if(typeof old_values !== "undefined" && old_values) {
                let oldval = $element.attr("data-oldvalue");
                obj[field] = typeof oldval !== "undefined" ? oldval : elementval;
            }
            else {
                obj[field] =  elementval;
            }
        });
        return obj;
    };

    HistoryMedicationsController.prototype.isTaper = function ($row) {
        return $row.hasClass('js-taper-row');
    };

    HistoryMedicationsController.prototype.setRowData = function ($row, data, excl_fields)
    {
        let self = this;
        let row_data_key = $row.attr('data-key');
        let taper_data_key = "";
        if (this.isTaper($row)) {
            row_data_key = $row.attr("data-parent-key");
            taper_data_key = "[taper][" + $row.attr("data-taper-key") + "]";
        }
        $.each(this.fields, function(i, field){
            if(typeof excl_fields === 'undefined' || excl_fields.indexOf(field) === -1) {
                if(typeof data[field] !== "undefined") {
                    let $input = $("[name='"+self.options.modelName+"[entries]["+ row_data_key +"]"+ taper_data_key + "["+field+"]']");
                    $input.val(data[field]);
                    if(field === "laterality") {
                        $row.find(self.options.routeOptionWrapperSelector).find('input').each(function(){
                                if($(this).val() === data[field] || data[field] === "3") {
                                    $(this).prop( "checked", true);
                                } else {
                                    $(this).prop( "checked", false);
                                }
                        });
                    }
                    if (field === "dispense_condition_id") {
                        self.getDispenseLocation($row.find('.js-dispense-condition'), data['dispense_location_id']);
                    }
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
       let controller = this;
       let patient_allergies = controller.options.patientAllergies;
       let merged_allergies = {};
       $.each(patient_allergies, function (i, e) {
            merged_allergies[e] = controller.options.allAllergies[e];
       });

       let $element = $("#OEModule_OphCiExamination_models_Allergies_element");

       if($element.length > 0) {
           let allergiesController = $element.data("controller");
           $.each(allergiesController.getAllergyIds(), function (i, e) {
               merged_allergies[e] = controller.options.allAllergies[e];
           });
       }

       return merged_allergies;
    };

    HistoryMedicationsController.prototype.createAllergiesDialog = function(allergic_drugs)
    {
        return new OpenEyes.UI.Dialog.Confirm({
            content: "Patient is allergic to " +
                allergic_drugs.join() +
                ". Are you sure you want to add them?"
        });
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
        let allergies = this.getMergedAllergies();
        let allergic_drugs = [];
        let controller = this;

        $.each(selectedItems, function (i, medication) {
            if(typeof medication.allergy_ids === "undefined" || medication.allergy_ids.toString() === "") {
                return;
            }

            let med_allergies = medication.allergy_ids.toString().split(",");
            let intersect = [];

            $.each(med_allergies, function (j, med_allergy) {
                for(let k in allergies) {
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

            let dialog = controller.createAllergiesDialog(allergic_drugs);

            dialog.on('ok', function () {
                controller.addEntry(selectedItems);
            });

            dialog.on('cancel' , function(){
                let items_to_add = [];

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

    HistoryMedicationsController.prototype.addSet = function (set_id) {
        let controller = this;
        $.getJSON(controller.options.drugSetFormSource, {
            set_id: set_id,
        }, function (response) {
            let rows = controller.createRow(response);
            response.forEach(function(medication) {
                if(medication['tapers'] !== undefined) {
                    medication['tapers'].forEach(function(taper) {
                        response.push(taper);
                    });
                }
            });

            for (let row_index in rows) {
                let $row = $(rows[row_index]);
                controller.addMedicationItemRow($row, response[row_index]);
                $row.find(".js-btn-prescribe").click();
            }

            controller.displayTableHeader();
        });
    };

    HistoryMedicationsController.prototype.getMatchingAllergies = function(medications, allergies) {
        let same_allergies = [];
        medications.forEach(function (medication) {
            medication['allergies'].forEach(function (allergy) {
                if (inArray(allergy, allergies)) {
                    same_allergies.push(medication.label);
                }
            });
        });

        return same_allergies;
    };

    HistoryMedicationsController.prototype.processSetEntries = function(set_id) {
        let controller = this;
        let allergies = controller.getMergedAllergies();
        $.get(baseUrl + "/OphDrPrescription/PrescriptionCommon/getSetDrugs",{
            set_id: set_id
        }, function (response) {
            if (typeof allergies !== undefined) {
                let medications = JSON.parse(response);
                let matching_allergies = controller.getMatchingAllergies(medications, allergies);

                if (matching_allergies.length > 0) {
                    let dialog = controller.createAllergiesDialog(matching_allergies);
                    dialog.on('ok', function () {
                        controller.addSet(set_id);
                    }.bind(this));
                    dialog.open();
                } else {
                    controller.addSet(set_id);
                }
            } else {
                controller.addSet(set_id);
            }
        });
    };

    HistoryMedicationsController.prototype.updateTextualDisplay = function ($row) {
        let displayDoseText = "";
        if($row.find(".js-dose").val() !== '') {
            displayDoseText = $row.find(".js-dose").val() + " " + $row.find(".js-dose-unit-term").text();
        }
        $row.find(".js-textual-display-dose").text(displayDoseText);
        if($row.find(".js-frequency").val() !== ''){
            $row.find(".js-textual-display-frequency").text($row.find(".js-frequency option:selected").text());
        }
        let route_lat = "";
        let $lat_ctrl = $row.find(".admin-route-options");
        if($lat_ctrl.val() !== "") {
            route_lat = $lat_ctrl.find("option:selected").text()+" ";
        }
        route_lat+=$row.find(".js-route option:selected").text();
        $row.find(".js-textual-display-route-laterality").text(route_lat);
    };

    HistoryMedicationsController.prototype.updateAllergyStatuses = function(new_allergy_ids)
    {
        let allergies = this.getMergedAllergies();
        let controller = this;
        let matched_allergy_ids = [];

        let match_allergies = function($row, allergy_ids) {
            let intersection = [];
            $.each(allergy_ids, function(i, id) {
                for(let j in allergies) {
                    if(id == j)  {
                        intersection.push(id);
                        matched_allergy_ids.push(parseInt(id));
                    }
                }
                intersection = controller.getIdsFromAllergiesElement(intersection, id);
                matched_allergy_ids = controller.getIdsFromAllergiesElement(matched_allergy_ids, id);
            });

            $row.find(".js-allergy-warning").remove();

            if(intersection.length > 0) {
                let allergy_warning = controller.getAllergyWarningForAllergyIds(intersection);
                $(allergy_warning).prependTo($row.find(".js-prepended_markup"));
                return true;
            }
            else {
                return false;
            }
        };

        $.each(this.$table.find("tbody > tr"), function (i, row) {
            let allergy_ids = $(row).attr("data-allergy-ids");
            let allergies = [];
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
        let dose_unit_dropdown_disabled = ($origin.find('select.js-unit-dropdown').attr('disabled') === "disabled");
        let $row = $(this.boundController.createRow(undefined , dose_unit_dropdown_disabled));
        $row.appendTo($target);
        let data = this.getRowData($origin, old_values);
        data.show_dose_units = !dose_unit_dropdown_disabled;
        data.usage_type = $target.attr("data-usage-type");

        this.boundController.setRowData($row, data);
        this.boundController.initialiseRowEventTriggers($row, data);
        if(data.end_date !== "") {
            this.showStopControls($row);
        }
        this.updateRowRouteOptions($row, false);

        $row.find(".js-prepended_markup:visible").load("/medicationManagement/getInfoBox?medication_id="+data.medication_id);
        this.disableRemoveButton($row);

        return $row;
    };

    HistoryMedicationsController.prototype.getRandomBoundKey = function() {
			let uniqueKeyFound = false;
			let randomKey;
			while(!uniqueKeyFound) {
				randomKey = generateId();
				uniqueKeyFound = true;
				$.each($(window).find('.js-bound-key'), function(index, $boundKey){
					if(randomKey === $boundKey.val()){
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
	  		let randomBoundKey = $row1.find('.js-bound-key').val();

	  		if(!randomBoundKey) {
					randomBoundKey = this.getRandomBoundKey();
				}

	  		$row1.find('.js-bound-key').val(randomBoundKey);
	  		$row2.find('.js-bound-key').val(randomBoundKey);
	  	}
	  	$row1.data("bound_entry", $row2);
	  	$row2.data("bound_entry", $row1);
	  };

    HistoryMedicationsController.prototype.updateBoundEntry = function ($row, callback)
    {
        let $bound_entry = $row.data("bound_entry");
        if(typeof $bound_entry === "undefined") {
            return;
        }
        let data = this.getRowData($row);
        let controller = $bound_entry.closest(".element-fields").data("controller_instance");
        controller.setRowData($bound_entry, data);
        // controller.updateRowRouteOptions($bound_entry);

        if(data.end_date !== "") {
            controller.showStopControls($bound_entry);
        }

        if(callback !== undefined) {
            callback($bound_entry, controller);
        }
    };

	HistoryMedicationsController.prototype.removeBoundEntry = function ($row) {
		let $bound_entry = $row.data("bound_entry");
		if (typeof $bound_entry === "undefined") {
			return;
		}

		let key = $bound_entry.data('key');
		let $full_entry = $bound_entry.parent().find('tr[data-key=' + key + ']');
		$full_entry.remove();
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
      $.getJSON('/OphCiExamination/Risks/forSets', { set_ids: setIds }, res => {
          this.addDrugForRisks(drug_name, res);
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
      let risksMap = [];
      for (let i=0; i < risks.length; i++) {
          risksMap.push({id: risks[i].id, comments: [drugName], risk_name: risks[i].name});
      }

      //checking the risksMap.length because HistoryRisksCore (js class) will automatically open the element if it isn't there
      if(risksMap.length){
          exports.HistoryRisks.addRisksForSource(risksMap, 'Medications');
      }

  };


  HistoryMedicationsController.prototype.showDate = function($row, $type)
  {
    let $wrapper = $row.find('.' + $type + '-date-wrapper');
    $wrapper.show();
    let $fuzzyFieldset = $wrapper.parents('fieldset');
    let date = OpenEyes.Util.dateFromFuzzyFieldSet($fuzzyFieldset);
    $fuzzyFieldset.find('input[type="hidden"]').val(date);
    $fuzzyFieldset.find('.enable').hide();
    $fuzzyFieldset.find('.cancel').show();
  }

  HistoryMedicationsController.prototype.cancelDate = function($row, $type)
  {
    let $wrapper = $row.find('.' + $type + '-date-wrapper');
    $wrapper.hide();
    let $fuzzyFieldset = $wrapper.parents('fieldset');
    $fuzzyFieldset.find('input[type="hidden"]').val('');
    $fuzzyFieldset.find('.enable').show();
    $fuzzyFieldset.find('.cancel').hide();
  };

  HistoryMedicationsController.prototype.updateRowRouteOptions = function($row, reset_values = true)
  {
      let $routeOptionWrapper = $row.find(this.options.routeOptionWrapperSelector);
      $routeOptionWrapper.hide();

      if(reset_values) {
          $routeOptionWrapper.find('input').each(function () {
              $(this).prop("checked", false);
          });
          $row.find(this.options.routeOptionInputSelector).val('');
      }

      let value = $row.find(this.options.routeFieldSelector + ' option:selected').val();
      if (value !== "" && typeof value !== "undefined") {
          $.getJSON(this.options.routeOptionSource, {route_id: value}, function(data) {
              if (data.length) {
                  $routeOptionWrapper.show();
              }
          });
      }
  };

  HistoryMedicationsController.prototype.createRow = function(medications, has_dose_unit_term = false)
  {
      let newRows = [];
      let template = this.templateText;
      let taperTemplate = this.taperTemplateText;
      let element = this.$element;
      let data = {};

      if(typeof medications === "undefined") {
          // just create an empty row
          data.row_count = OpenEyes.Util.getNextDataKey( this.$element.find('table tbody tr'), 'key');
          data.has_dose_unit_term = has_dose_unit_term;

          return Mustache.render(
              this.templateText,
              data
          );
      }

    for (let i in medications) {
      data = medications[i];
      data['tapers'] = medications[i]['tapers'];
      data['row_count'] = OpenEyes.Util.getNextDataKey( element.find('table tbody tr'), 'key')+ newRows.length;
      this.processRisks(medications[i]['set_ids'.split(",")], medications[i]['medication_name']);
      data['allergy_warning'] = this.getAllergyWarning(medications[i]);
      data['bound_key'] = this.getRandomBoundKey();
      data['has_dose_unit_term'] = typeof medications[i]['dose_unit_term'] !== 'undefined' && medications[i]['dose_unit_term'] !== "" ;
      data['allergy_ids'] = medications[i]['allergy_ids'];

      newRows.push(Mustache.render(
          template,
          data));

      if (data['tapers'] !== undefined) {
          data['tapers'].forEach(function(taper, taper_key) {
              taper['row_count'] = data['row_count'];
              taper['taper_count'] = taper_key;
              newRows.push(Mustache.render(
                  taperTemplate,
                  taper
              ));
          });
      }

    }

    return newRows;
  };

    HistoryMedicationsController.prototype.getIdsFromAllergiesElement = function(intersect, med_allergy) {
        let $allergies_rows = $('#OEModule_OphCiExamination_models_Allergies_entry_table').find('tr').not('#OEModule_OphCiExamination_models_Allergies_no_allergies_wrapper');

        if ($allergies_rows) {
            $allergies_rows.each(function (row_index, row) {
                let name = 'OEModule_OphCiExamination_models_Allergies[entries][' + $(row).data('key') + '][allergy_id]';
                let value = $(row).find('input[name="' + name + '"]').val();
                if (value == med_allergy && !intersect.includes(value)) {
                    intersect.push(value);
                }
            });
        }

        return intersect;
    };

    HistoryMedicationsController.prototype.getAllergyWarning = function(medication)
    {
        if(typeof medication.allergy_ids === "undefined" || medication.allergy_ids.toString() === "") {
            return "";
        }

        let patient_allergies = this.getMergedAllergies();
        let med_allergies = medication.allergy_ids.toString().split(",");
        let intersect = [];
        let allergy_names = [];
        let controller = this;

        $.each(med_allergies, function (j, med_allergy) {
            for(let k in patient_allergies) {
                if(k == med_allergy) {
                    intersect.push(k);
                }
            }
            intersect = controller.getIdsFromAllergiesElement(intersect, med_allergy);
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
        let allergy_names = [];
        let controller = this;

        $.each(allergy_ids, function (i, e) {
            allergy_names.push(controller.options.allAllergies[e]);
        });

        return '<i class="oe-i warning small pad js-has-tooltip js-allergy-warning" data-tooltip-content="Allergic to ' + allergy_names.join(", ") + '"></i>';
    };

    HistoryMedicationsController.prototype.addMedicationItemRow = function ($row, medication, do_callback = false) {
        $row.appendTo(this.$currentMedicationsTable.children('tbody'));
        this.setRowData($row, medication);
        let $lastRow = this.$currentMedicationsTable.find('tbody tr.js-first-row:last');

        if (!this.isTaper($row)) {
            this.initialiseRowEventTriggers($lastRow);
            this.loadDrugDefaults($lastRow, medication);
        }

        if(do_callback) {
            this.options.onAddedEntry($lastRow, this);
        }
        return $lastRow;
    };

    HistoryMedicationsController.prototype.addEntry = function (selectedItems, do_callback)
    {
        let controller = this;
        let medication = [];

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
										source_subtype: selectedItems[i].source_subtype
                };
            }
            else {
                // added as a copy of another row
                medication[i] = JSON.parse(JSON.stringify(selectedItems[i]));
            }

        });

        let rows = controller.createRow(medication);
        let new_rows = [];
        rows.forEach(function (row, index) {
            new_rows[index] = controller.addMedicationItemRow($(row), medication[index], do_callback);
        });

        $(controller.options.medicationSelectOptions).find('.selected').removeClass('selected');
        $(controller.options.medicationSearchInput).val('');
        $(controller.options.medicationSearchResult).empty();

        controller.displayTableHeader();
        return new_rows;
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

    HistoryMedicationsController.prototype.displayTableHeader = function () {
        let table_header = this.$table.find("thead");

        if (this.$table.find("tbody tr").length > 0) {
            table_header.show();
        } else {
            table_header.hide();
        }
    };

    HistoryMedicationsController.prototype.setBoundEntryStop = function ($bound_entry) {
    		let key = $bound_entry.data('key');
    		let $second_part_of_bound_entry = $bound_entry.parent().find('tr[data-key=' + key + '].js-second-row');
        let stop_reason = $("option:contains('Medication parameters changed')").attr("value");
        this.showStopControls($second_part_of_bound_entry);
			$second_part_of_bound_entry.find('.js-stop-reason').attr('value', stop_reason);
    };

    HistoryMedicationsController.prototype.enableManualRowDeletion = function ($row) {
        let $trash = $row.find('.trash');
        $trash.removeClass('disabled');
        $trash.parent().removeClass('js-has-tooltip');
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
    let controller = this;

    controller.$element.on('click', controller.options.detailToggleSelector, function(e) {
      e.preventDefault();
      let $dataDisplay = controller.$element.find('.' + $(this).data('kind'));
      $dataDisplay.find('.detail').toggle();
      $dataDisplay.find('.simple').toggle();
      $(this).find('.fa').toggleClass('fa-expand fa-compress');
    });

    controller.$element.on('click', controller.options.kindToggleSelector, function(e) {
        e.preventDefault();
        let $kindDisplay = controller.$element.find('.' + $(this).data('kind') + '-kind');
        $kindDisplay.toggle();
        let overflowContainer = controller.$element.parents('.oe-popup-overflow');
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
