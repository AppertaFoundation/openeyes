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

    function SystemicDiagnosesController(options) {
        this.options = $.extend(true, {}, SystemicDiagnosesController._defaultOptions, options);

        this.$element = this.options.element;
        this.$table = this.$element.find('#OEModule_OphCiExamination_models_SystemicDiagnoses_diagnoses_table');
        this.templateText = $('#OEModule_OphCiExamination_models_SystemicDiagnoses_template').text();
        this.$popupSelector = $('#systemic-diagnoses-popup');
        this.initialiseTriggers();
        this.initialiseDatepicker();
    }

    SystemicDiagnosesController._defaultOptions = {
        modelName: 'OEModule_OphCiExamination_models_SystemicDiagnoses',
        element: undefined,
        addButtonSelector: '#add-history-systemic-diagnoses',
        searchSource: '/medication/finddrug',
        selectOptions: '.select-options',
        selectItems: '#systemic-diagonses-option',
        searchOptions: '.systemic-diagonses-search-options',
        searchInput: '#systemic-diagonses-search-field',
        searchResult: '#systemic-diagonses-search-results'
    };

    SystemicDiagnosesController.prototype.initialiseTriggers = function()
    {
        var controller = this;

        // removal button for table entries
        controller.$table.on('click', 'i.trash', function(e) {
            e.preventDefault();
            $(e.target).parents('tr').remove();
        });

        // setup current table row behaviours
        controller.$table.find('tbody tr').each(function() {
            controller.initialiseRow($(this));

        });
      controller.$element.on('click','#systemic-diagonses-search-btn',  function (e) {
        if ($(this).hasClass('selected')) {
          return;
        }

        $(this).addClass('selected');
        $('#history-medication-select-btn').removeClass('selected');

        $(controller.options.searchOptions).show();
        $(controller.options.searchOptions).find('selected').remove('selected');
        $(controller.options.selectOptions).hide();
      });

      controller.$element.on('click','#systemic-diagonses-select-btn', function () {
        if ($(this).hasClass('selected')) {
          return;
        }

        $(this).addClass('selected');
        $('#history-medication-search-btn').removeClass('selected');

        $(controller.options.selectOptions).show();
        $(controller.options.searchOptions).hide();
        $(controller.options.searchInput).val('');
        $(controller.options.searchResult).empty();
      });

      $(controller.options.searchInput).on('change keyup', function () {
        controller.initialiseSearch();
      });

    };
    SystemicDiagnosesController.prototype.initialiseDatepicker = function () {
      var row_count = OpenEyes.Util.getNextDataKey( this.$element.find('table tbody tr'), 'key');
      for (var i=0; i<row_count;i++){
          var datepicker_name = '#systemic-diagnoses-datepicker-'+i;
          var datepicker= $(this.$table).find(datepicker_name);
          if (datepicker.length!=0){
              pickmeup(datepicker_name, {
                  format: 'Y-m-d',
                  hide_on_select: true,
                default_date: false
              });
              }}
    };

    SystemicDiagnosesController.prototype.setDatepicker = function () {
        var row_count = OpenEyes.Util.getNextDataKey( this.$element.find('table tbody tr'), 'key')-1;
        var datepicker_name = '#systemic-diagnoses-datepicker-'+row_count;
        var datepicker= $(this.$table).find(datepicker_name);
        if (datepicker.length!=0){
            pickmeup(datepicker_name, {
                format: 'Y-m-d',
              hide_on_select: true,
              default_date: false
            });
        }};

    SystemicDiagnosesController.prototype.initialiseRow = function($row)
    {
        var controller = this;
        var $radioButtons = $row.find('input[type=radio]');

        $row.on('change', '.fuzzy-date select', function(e) {
            var $fuzzyFieldset = $(this).closest('fieldset');
            var date = controller.dateFromFuzzyFieldSet($fuzzyFieldset);
            $fuzzyFieldset.find('input[type="hidden"]').val(date);
        });
        var DiagnosesSearchController = new OpenEyes.UI.DiagnosesSearchController({
            'inputField': $row.find('.diagnoses-search-autocomplete'),
            'fieldPrefix': $row.closest('section').data('element-type-class')
        });
        $row.find('.diagnoses-search-autocomplete').data('DiagnosesSearchController', DiagnosesSearchController );
      // radio buttons
        $radioButtons.on('change', function(e){
          $(e.target).parent().siblings('tr input[type="hidden"]').val($(e.target).val());
        });
    };

    SystemicDiagnosesController.prototype.initialiseSearch = function () {

    };
    SystemicDiagnosesController.prototype.dateFromFuzzyFieldSet = function(fieldset)
    {
        var res = fieldset.find('select.fuzzy_year').val();
        var month = parseInt(fieldset.find('select.fuzzy_month option:selected').val());
        res += '-' + ((month < 10) ? '0' + month.toString() : month.toString());
        var day = parseInt(fieldset.find('select.fuzzy_day option:selected').val());
        res += '-' + ((day < 10) ? '0' + day.toString() : day.toString());

        return res;
    };

    SystemicDiagnosesController.prototype.createRow = function()
    {
        var controller = this;
        var selected_options = [];
        var newRows = [];
        var template = this.templateText;
        var element = this.$element;
        $(controller.options.selectItems).find('.selected').each(function (e) {
          selected_options.push(this);
        });
        $(controller.options.searchResult).find('.selected').each(function (e) {
          selected_options.push(this);
        });

        for (var i in selected_options) {
            data = {};
            data['row_count'] = OpenEyes.Util.getNextDataKey(element.find('table tbody tr'), 'key')+ newRows.length;
            data['disorder_id'] = $(selected_options[i]).data('id');
            data['disorder_display'] = $(selected_options[i]).data('str');
            newRows.push( Mustache.render(
            template,
            data ));
        }

        return newRows;
    };

    SystemicDiagnosesController.prototype.addEntry = function()
    {
        var rows = this.createRow();
      for(var i in rows){
        this.$table.find('tbody').append(rows[i]);
        this.initialiseRow(this.$table.find('tbody tr:last'));
        this.setDatepicker();
      }

      $(this.options.selectOptions).find('.selected').removeClass('.selected');
      $(this.options.searchInput).val('');
      $(this.options.searchResult).empty();
    };

    exports.SystemicDiagnosesController = SystemicDiagnosesController;
})(OpenEyes.OphCiExamination);
