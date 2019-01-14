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

OpenEyes.OphCiExamination.PreviousSurgeryController = (function() {

    function PreviousSurgeryController(options) {

        this.options = $.extend(true, {}, PreviousSurgeryController._defaultOptions, options);

        //TODO: these should be driven by  options
        this.$section = $('section.' + this.options.modelName);
        this.tableSelector = '#' + this.options.modelName + '_operation_table';
        this.$table = $('#' + this.options.modelName + '_operation_table');
        this.fuzyDateWrapperSelector = this.options.modelName + '_fuzzy_date';
        this.$popupSelector = $('#add-to-past-surgery');
        this.templateText = $("#OEModule_OphCiExamination_models_PastSurgery_operation_template").text();
        this.initialiseTriggers();
        this.initialiseDatepicker();
    }

    PreviousSurgeryController._defaultOptions = {
        modelName: 'OEModule_OphCiExamination_models_PastSurgery',
        monthNames: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
    };
  /**
   * Setup Datepicker
   */
  PreviousSurgeryController.prototype.initialiseDatepicker = function () {
      var row_count =  OpenEyes.Util.getNextDataKey( this.tableSelector + ' tbody tr', 'key') ;
      for (var i=0; i<row_count;i++){
        this.constructDatepicker( i);
      }
  };
  PreviousSurgeryController.prototype.setDatepicker = function () {
    var row_count =  OpenEyes.Util.getNextDataKey( this.tableSelector + ' tbody tr', 'key')-1 ;
    this.constructDatepicker(row_count);
  };

  PreviousSurgeryController.prototype.constructDatepicker = function (line_no) {
    var datepicker_name = '#past-surgery-datepicker-'+line_no;
    var datepicker= $(this.tableSelector).find(datepicker_name);
    if (datepicker.length!=0){
      pickmeup(datepicker_name, {
        format: 'Y-m-d',
        hide_on_select: true,
        default_date: false
      });
    }
  };

  PreviousSurgeryController.prototype.initialiseTriggers = function(){

        var controller = this;
        controller.$popupSelector.on('click','.add-icon-btn', function(e) {
            e.preventDefault();
            controller.addEntry();
        });

        controller.$table.on('click', '.remove_item', function(e) {
            e.preventDefault();
            $(e.target).parents('tr').remove();
        });

        controller.$section.on('input', ('#'+controller.fuzyDateWrapperSelector), function(e) {
            var $fuzzy_fieldset = $(this).closest('fieldset');
            var date = controller.dateFromFuzzyFieldSet($fuzzy_fieldset);
            $fuzzy_fieldset.closest('td').find('input[type="hidden"]').val(date);
        });

        controller.$section.on('input', ('.'+controller.options.modelName + '_operations'), function(e) {
            var common_operation = $(this).find('option:selected').text();
            $(this).closest('td').find('.common-operation').val(common_operation);
            $(this).val(null);
        });
        controller.$table.on('click', ('.'+controller.options.modelName + '_previous_operation_side'), function(e) {
            $(e.target).parent().siblings('tr input[type="hidden"]').val($(e.target).val());
        });

        var eye_selector = new OpenEyes.UI.EyeSelector({
            element: controller.$section
        });

        controller.$table.data('eyeSelector', eye_selector);
    };

    /**
     *
     * @param data
     * @returns {*}
     */
    PreviousSurgeryController.prototype.createRow = function(selectedItems)
    {
      var newRows = [];
      var template = this.templateText;
      var tableSelector = this.tableSelector;
      $(selectedItems).each(function (e) {
        var data = {};
        data['row_count'] = OpenEyes.Util.getNextDataKey(tableSelector + ' tbody tr', 'key')+ newRows.length;
        data['id'] = this['id'];
        if (this['label']==='Other'){
          data['operation'] = '';
        } else {
          data['operation'] = this['label'];
        }
        newRows.push( Mustache.render(
          template,
          data ));
      });
      return newRows;

    };

    /**
     * Add a family history section if its valid.
     */
    PreviousSurgeryController.prototype.addEntry = function(selectedItems)
    {
        var rows= this.createRow(selectedItems);
        for(var i in rows){
          this.$table.find('tbody').append(rows[i]);

          let $operation = this.$table.find('tbody tr:last').find('.common-operation');
            if (!$operation.val()) {
                $operation.prop('type', 'text');
            }

          this.setDatepicker();
        }
        this.$popupSelector.find('.selected').removeClass('selected');
    };

    /**
     * Simple validation of selected values for a fuzzy date fieldset.
     *
     * @param fieldset
     * @returns {boolean}
     */
    PreviousSurgeryController.prototype.validateFuzzyDateFieldSet = function(fieldset)
    {
        if (parseInt(fieldset.find('select[name="fuzzy_day"] option:selected').val()) > 0) {
            if (!parseInt(fieldset.find('select[name="fuzzy_month"] option:selected').val()) > 0) {
                return false;
            }
        }
        return true;
    };

    /**
     * @TODO: should be common function across history elements
     * @param fieldset
     * @returns {*}
     */
    PreviousSurgeryController.prototype.dateFromFuzzyFieldSet = function(fieldset)
    {
        res = fieldset.find('select.fuzzy_year').val();
        var month = parseInt(fieldset.find('select.fuzzy_month option:selected').val());
        res += '-' + ((month < 10) ? '0' + month.toString() : month.toString());
        var day = parseInt(fieldset.find('select.fuzzy_day option:selected').val());
        res += '-' + ((day < 10) ? '0' + day.toString() : day.toString());

        return res;
    };

    /**
     *
     * @param dt yyyy-mm-dd
     * @returns {string}
     */
    PreviousSurgeryController.prototype.getFuzzyDateDisplay = function(dt)
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

    return PreviousSurgeryController;
})();