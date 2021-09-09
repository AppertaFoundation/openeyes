<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<div id="local_authority_search_wrapper" class="row field-row<?= $hidden ? ' hidden':''?>">
    <div class="large-8 column large-push-2 end">
    <?php
        $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
            'id' => 'la_auto_complete',
            'name' => 'la_auto_complete',
            'value' => '',
            'source' => "js:function(request, response) {
            var existing = [];
            $('#consultant_list').children('li').map(function() {
                existing.push(String($(this).data('id')));
            });

            $.ajax({
                'url': '".Yii::app()->createUrl('/OphCoCvi/localAuthority/autocomplete')."',
                'type':'GET',
                'data':{'term': request.term},
                'success':function(data) {
                    data = $.parseJSON(data);
                    if (!data.length) {
                        data = [
                        {
                            'label': 'No results found',
                            'value': response.term
                        }
                        ];
                    }
                    response(data);
                }
            });
            }",
            'options' => array(
                'minLength' => '3',
                'select' => "js:function(event, ui) {
                    updateLAFields(ui.item);
                    $('#la_auto_complete').val('');
                    return false;
                }",
            ),
            'htmlOptions' => array(
                'placeholder' => 'Type to search for local authority',
            ),
        ));
        ?>
    </div>
</div>
