/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

(function(exports) {

    /**
     * OpenEyes Form module
     * @namespace OpenEyes.Util
     * @memberOf OpenEyes
     */
    var Admin = {},
        listCache;

    Admin.cacheList = function($list){
        listCache = $list.html();
    };

    Admin.shortCodeSelect = function($shortCode, $textArea)
    {
        var cursorPos = 0,
            current = '';

        $textArea.on('blur', function(){
            cursorPos = $(this).prop('selectionEnd');
        });

        $shortCode.on('change', function(){
            if ($(this).val() !== '') {
                current = $textArea.val();
                $textArea.val(current.substring(0,cursorPos) + '[' + $(this).val() + ']' + current.substring(cursorPos,current.length));
                $(this).val('');
            }
        });
    };

    Admin.saveSorted = function(event, ui){
        var $form = ui.item.closest('form');
        $.ajax({
            'type': 'POST',
            'url': $('#et_sort').data('uri'),
            'data': $form.serialize(),
            'success': function(){
                Admin.cacheList($form.find('.sortable'));
            },
            'error': function (jqXHR, status) {
                $form.find('.sortable').html(listCache);
                alert(jqXHR.responseText);
            }
        });
    };

    Admin.setSubListSearchToDefault = function() {
        var $searchItems,
            defaultString;

        $searchItems = $(':input[name^="search"], :input[name^="default"]').not('[name*="compare_to"]').not('[name*="\\[value\\]"]');

        defaultString = $searchItems.serialize().replace(/search%5B/g, 'default%5B');
        if($('#returnUri').val()){
            defaultString += '&returnUri=' + $('#returnUri').val();
        }
        if(defaultString){
            $('#et_add').data('uri', $('#et_add').data('uri') + '?' + defaultString);
        }
    };

    Admin.init = function(){
        $('button[type="submit"]').on('click', function(){
            //Remove CSRF token if the form is going to be get
            var $form = $(this).closest('form');
            if($(this).attr('formmethod') === 'get' || $form.attr('method') === 'get'){
                $form.find('input[name="YII_CSRF_TOKEN"]').remove();
            }
        });

        Admin.setSubListSearchToDefault();
    };

    exports.Admin = Admin;

}(this.OpenEyes || {}));

$(document).ready(function(){
    OpenEyes.Admin.init();
});