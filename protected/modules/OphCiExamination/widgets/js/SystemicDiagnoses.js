/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2017
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

var OpenEyes = OpenEyes || {};

OpenEyes.OphCiExamination = OpenEyes.OphCiExamination || {};

OpenEyes.OphCiExamination.SystemicDiagnosesController = (function() {

  function SystemicDiagnosesController(options) {
    this.options = $.extend(true, {}, SystemicDiagnosesController._defaultOptions, options);
    this.$diagnosisDisplay = $(this.options['diagnosisDisplaySelector']);
    this.$sideFld = $(this.options['sideSelector']);
    this.$dateFieldSet = $(this.options['dateSelector']);
    this.$addEntryButton = $(this.options['addEntryButtonSelector']);
    this.$table = $(this.options['tableSelector']);

    this.templateText = $(this.options['templateSelector']).text();

    this.$addEntryButton.on('click', function(e) {
      e.preventDefault();
      if (this.validateForm()) {
        this.addDiagnosis();
      }
    }.bind(this));

    this.$table.on('click', '.button', function(e) {
      e.preventDefault();
      $(e.target).parents('tr').remove();
    });
  }

  SystemicDiagnosesController._defaultOptions = {
    monthNames: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
    addEntryButtonSelector: '.add-diagnosis',
    diagnosisDisplaySelector: '#enteredDiagnosisText',
    sideSelector: '.OEModule_OphCiExamination_models_SystemicDiagnoses_diagnosis_side',
    dateSelector: '.OEModule_OphCiExamination_models_SystemicDiagnoses_diagnosis_fuzzy_date',
    tableSelector: '#OEModule_OphCiExamination_models_SystemicDiagnoses_diagnoses_table',
    templateSelector: "#OEModule_OphCiExamination_models_SystemicDiagnoses_diagnosis_template"
  };

  SystemicDiagnosesController.prototype.selectDisorder = function(id, displayValue)
  {
    this.disorderId = id;
    this.disorderDisplay = displayValue;
  };

  /**
   * Set current disorder to null
   */
  SystemicDiagnosesController.prototype.unselectDisorder = function()
  {
    this.disorderId = undefined;
    this.disorderDisplay = undefined;
  };

  /**
   * If a day is selected, a month is required.
   *
   * @param fieldset
   * @returns {boolean}
   */
  SystemicDiagnosesController.prototype.validateFuzzyDateFieldSet = function(fieldset)
  {
    if (parseInt(fieldset.find('select[name="fuzzy_day"] option:selected').val()) > 0) {
      if (!parseInt(fieldset.find('select[name="fuzzy_month"] option:selected').val()) > 0) {
        return false;
      }
    }
    return true;
  };

  /**
   * Check a valid entry has been created.
   *
   * @returns {boolean}
   */
  SystemicDiagnosesController.prototype.validateForm = function()
  {
    if (!this.disorderId) {
      new OpenEyes.UI.Dialog.Alert({
        content: "Please select a diagnosis."
      }).open();
      return false;
    }
    if (!this.validateFuzzyDateFieldSet(this.$dateFieldSet)) {
      new OpenEyes.UI.Dialog.Alert({
        content: "Please select a valid date for the diagnosis."
      }).open();
      return false;
    }
    return true;
  };

  /**
   * Resets the form for user to add the next entry
   */
  SystemicDiagnosesController.prototype.resetForm = function()
  {
    this.$diagnosisDisplay.find('.clear-diagnosis-widget').trigger('click');
    this.$sideFld.prop('checked', false);
    this.$dateFieldSet.find('select[name="fuzzy_day"] option:selected').prop('selected', false);
    this.$dateFieldSet.find('select[name="fuzzy_month"] option:selected').prop('selected', false);
    this.$dateFieldSet.find('select[name="fuzzy_year"] option:last').prop('selected', true);
  };

  /**
   * Simple wrapper to generate table row content from the template.
   *
   * @param data
   * @returns {*}
   */
  SystemicDiagnosesController.prototype.createRow = function(data)
  {
    indices = this.$table.find('tr').map(function () { return $(this).data('index'); });

    data.index = indices.length ? Math.max.apply(null, indices) + 1 : 0;

    return Mustache.render(
      template = this.templateText,
      data
    );
  };


  /**
   * @TODO: determine if we need to be smarter about date parsing to submit in the form.
   * @param fieldset
   * @returns {*}
   */
  SystemicDiagnosesController.prototype.dateFromFuzzyFieldSet = function(fieldset)
  {
    res = fieldset.find('select[name="fuzzy_year"]').val();
    var month = parseInt(fieldset.find('select[name="fuzzy_month"] option:selected').val());
    res += '-' + ((month < 10) ? '0' + month.toString() : month.toString());
    var day = parseInt(fieldset.find('select[name="fuzzy_day"] option:selected').val());
    res += '-' + ((day < 10) ? '0' + day.toString() : day.toString());

    return res;
  };

  /**
   *
   * @param dt yyyy-mm-dd
   * @returns {string}
   */
  SystemicDiagnosesController.prototype.getFuzzyDateDisplay = function(dt)
  {
    var res = [],
      bits = dt.split('-');

    if(bits[2] != '00') {
      res.push(parseInt(bits[2]).toString());
    }

    if(bits[1] != '00') {
      res.push(this.options.monthNames[parseInt(bits[1])-1]);
    }
    res.push(bits[0]);

    return res.join(' ');
  };

  /**
   * Builds the data structure for the table row.
   *
   * @returns {{}}
   */
  SystemicDiagnosesController.prototype.generateDataFromForm = function()
  {
    var data = {};
    data.disorder_id = this.disorderId;
    data.disorder_display = this.disorderDisplay;
    data.side_id = this.$sideFld.filter(':checked').val();
    data.side_display = this.$sideFld.filter(':checked').closest('label').text();
    data.date = this.dateFromFuzzyFieldSet(this.$dateFieldSet);
    data.date_display = this.getFuzzyDateDisplay(data.date);
    return data;
  };

  /**
   * Action method to parse from and create entry.
   */
  SystemicDiagnosesController.prototype.addDiagnosis = function()
  {
    // create table row
    var tableRow = this.createRow(this.generateDataFromForm());
    console.log(tableRow);
    this.$table.append(tableRow);
    // then reset
    this.resetForm();
  };

  return SystemicDiagnosesController;
})();

OpenEyes.OphCiExamination.SystemicDiagnosesSelectDiagnosis = function(event, ui) {
  if (ui !== undefined) {
    $('#OphCiExamination_SystemicDiagnoses').data('controller').selectDisorder(ui.item.id, ui.item.value);
  } else {
    $('#OphCiExamination_SystemicDiagnoses').data('controller').unselectDisorder();
  }

};

$(document).ready(function() {
  $('#OphCiExamination_SystemicDiagnoses').data('controller', new OpenEyes.OphCiExamination.SystemicDiagnosesController());
});