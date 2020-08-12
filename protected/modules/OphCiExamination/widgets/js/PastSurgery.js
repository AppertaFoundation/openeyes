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
        this.$noPastSurgeryFld = $('.' + this.options.modelName + '_no_pastsurgery');
        this.$noPastSurgeryWrapper = $('.' + this.options.modelName + '_no_pastsurgery_wrapper');
        this.$commentFld = $('#' + this.options.modelName + '_comments');
        this.$commentWrapper = $('#' + this.options.modelName + '-comments');
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
    };
  /**
   * Setup Datepicker
   */
  PreviousSurgeryController.prototype.initialiseDatepicker = function () {
      let row_count =  OpenEyes.Util.getNextDataKey( this.tableSelector + ' tbody tr', 'key') ;
      for (let i=0; i<row_count;i++){
        this.constructDatepicker( i);
      }
  };
  PreviousSurgeryController.prototype.setDatepicker = function () {
    let row_count =  OpenEyes.Util.getNextDataKey( this.tableSelector + ' tbody tr', 'key')-1 ;
    this.constructDatepicker(row_count);
  };

  PreviousSurgeryController.prototype.constructDatepicker = function (line_no) {
    let datepicker_name = '#past-surgery-datepicker-'+line_no;
    let datepicker= $(this.tableSelector).find(datepicker_name);
    if (datepicker.length!=0){
      pickmeup(datepicker_name, {
        format: 'Y-m-d',
        hide_on_select: true,
        default_date: false
      });
    }
  };

  PreviousSurgeryController.prototype.initialiseTriggers = function(){

        let controller = this;

      $(document).ready(function () {
          if(controller.$noPastSurgeryFld.prop('checked')) {
              controller.$table.find('tr:not(:first-child)').hide();
              controller.$popupSelector.hide();
          }
          controller.hideNoPastSurgery();
      });

        controller.$popupSelector.on('click','.add-icon-btn', function(e) {
            e.preventDefault();
            controller.addEntry();
        });

        controller.$table.on('click', '.remove_item', function(e) {
            e.preventDefault();
            $(e.target).parents('tr').remove();
            controller.showNoPastSurgery();
        });

        controller.$section.on('input', ('#'+controller.fuzyDateWrapperSelector), function(e) {
            let $fuzzy_fieldset = $(this).closest('fieldset');
            let date = OpenEyes.Util.dateFromFuzzyFieldSet($fuzzy_fieldset);
            $fuzzy_fieldset.closest('td').find('input[type="hidden"]').val(date);
        });

        controller.$section.on('input', ('.'+controller.options.modelName + '_operations'), function(e) {
            let common_operation = $(this).find('option:selected').text();
            $(this).closest('td').find('.common-operation').val(common_operation);
            $(this).val(null);
        });
        controller.$table.on('click', ('.'+controller.options.modelName + '_previous_operation_side'), function(e) {
            $(e.target).parent().siblings('tr input[type="hidden"]').val($(e.target).val());
        });

      controller.$noPastSurgeryFld.on('click', function () {
          if (controller.$noPastSurgeryFld.prop('checked')) {
              controller.$table.find('tr:not(:first-child)').hide();
              controller.$popupSelector.hide();
              controller.$commentWrapper.hide();
          } else {
              controller.$popupSelector.show();
              if(controller.$commentFld.val()) {
                  controller.$commentWrapper.show();
              }
              controller.$table.find('tr:not(:first-child)').show();
          }
      });

      controller.$popupSelector.on('click', function (e) {
          e.preventDefault();
          controller.hideNoPastSurgery();
          if(controller.$table.hasClass('hidden')){
              controller.$table.removeClass('hidden');
          }
          controller.$table.show();
      });

        let eye_selector = new OpenEyes.UI.EyeSelector({
            element: controller.$section
        });

        controller.$table.data('eyeSelector', eye_selector);
    };

    PreviousSurgeryController.prototype.hideNoPastSurgery = function() {
        if (this.$table.find('tbody tr').length > 0) {
            this.$noPastSurgeryFld.prop('checked', false);
            this.$noPastSurgeryWrapper.hide();
        }
    };

    PreviousSurgeryController.prototype.showNoPastSurgery = function() {
        if (this.$table.find('tbody tr').length === 0) {
            this.$noPastSurgeryWrapper.show();
        } else {
            this.hideNoPastSurgery();
        }
    };

    /**
     *
     * @param data
     * @returns {*}
     */
    PreviousSurgeryController.prototype.createRow = function(selectedItems)
    {
      let newRows = [];
      let template = this.templateText;
      let tableSelector = this.tableSelector;
      $(selectedItems).each(function (e) {
        let data = {};
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
        let rows= this.createRow(selectedItems);
        for(let i in rows){
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

    return PreviousSurgeryController;
})();
