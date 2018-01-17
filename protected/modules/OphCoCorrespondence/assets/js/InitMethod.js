/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

var OpenEyes = OpenEyes || {};

OpenEyes.OphCoCorrespondence = OpenEyes.OphCoCorrespondence || {};


(function(exports) {

    function InitMethodController(options) {
        this.options = $.extend(true, {}, InitMethodController._defaultOptions, options);

        this.tableSelector = '#' + this.options.modelName + '_table';
        this.$table = $(this.tableSelector);
        this.templateText = $('#' + this.options.modelName + '_template').text();

        this.initialiseTriggers();
    }

    InitMethodController.prototype.initialiseTriggers = function()
    {
        var controller = this;
        $('#' + controller.options.modelName + '_add').on('click', function(e) {
            e.preventDefault();
            controller.addNewRow();
        });

        this.$table.on('click', 'button.remove', function(e) {
            e.preventDefault();
            controller.setErasableRow( $(this).closest('tr') );
        });

        this.$table.on('change', 'select', function(e) {
            controller.getInitMethodData( this );
        });

    }

    InitMethodController._defaultOptions = {
        modelName: 'OEModule_OphCoCorrespondence_models_OphcorrespondenceInitMethod',
        assocModelName: 'OEModule_OphCoCorrespondence_models_MacroInitAssociatedContent'
    };

    InitMethodController.prototype.addNewRow = function()
    {
        this.$table.find('tbody').append(this.createRow());
    };

    InitMethodController.prototype.setErasableRow = function( row )
    {
        var controller = this;
        var row_id = (OpenEyes.Util.getNextDataKey( row, 'key')) - 1;
        erase_row = '<input type="hidden" name="delete_associated['+row_id+'][delete]" value="'+ $('#' + controller.options.assocModelName + '_'+ row_id +'_id').val() +'" />';
        this.$table.find('tbody').append( erase_row  );

        row.remove();
    }

    /**
     *
     * @param data
     * @returns {*}
     */
    InitMethodController.prototype.createRow = function(data)
    {
        if (data === undefined)
            data = {};

        data['row_count'] = OpenEyes.Util.getNextDataKey( this.tableSelector + ' tbody tr', 'key');
        data['is_print_appended_js'] = 'CHECKED';

        return Mustache.render(
            this.templateText,
            data
        );

    };

    InitMethodController.prototype.getInitMethodData = function( select )
    {
        var controller = this;
        var $select = $(select);

        var row_id = (OpenEyes.Util.getNextDataKey( $select.closest('tr'), 'key')) - 1;

        if($select.val() > 0){
            $.ajax({
                'type': 'POST',
                'url': baseUrl + '/OphCoCorrespondence/admin/getInitMethodDataById',
                'data' :{YII_CSRF_TOKEN: YII_CSRF_TOKEN, id: $select.val() },
                'success': function(response) {
                    if(response.success == 1){
                        $('#' + controller.options.modelName + '_'+ row_id +'_short_code').val( response.short_code );
                        $('#' + controller.options.modelName + '_'+ row_id +'_method_id').val( $select.val() );

                        if( $('#' + controller.options.assocModelName + '_'+ row_id +'_is_print_appended').is(':checked') ){
                            $('#' + controller.options.modelName + '_'+ row_id +'_title').val( response.description.replace("Last ", "").replace(" Event","") );
                        }
                    }
                }
            });
        }
    }

    exports.InitMethodController = InitMethodController;

})(OpenEyes.OphCoCorrespondence);
