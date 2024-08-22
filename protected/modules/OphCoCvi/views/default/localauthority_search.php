<?php
/**
 * (C) Copyright Apperta Foundation 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2021, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<div id="local_authority_search_wrapper" class="row field-row<?= $hidden ? ' hidden':''?>">
    <div class="large-8 column large-push-2 end">
    <?php
        $this->widget('application.widgets.AutoCompleteSearch',
            [
                'field_name' => 'la_auto_complete',
                'htmlOptions' =>
                    [
                        'placeholder' => 'Type to search for local authority',
                    ],
                'layoutColumns' => ['field' => '12']
            ]);
        ?>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        OpenEyes.UI.AutoCompleteSearch.init({
            input: $('#la_auto_complete'),
            url: '/OphCoCvi/localAuthority/autocomplete',
            params: {
            },
            maxHeight: '200px',
            onSelect: function () {
                let response = OpenEyes.UI.AutoCompleteSearch.getResponse();
                let input = OpenEyes.UI.AutoCompleteSearch.getInput();

                updateLAFields(response);
                $('#la_auto_complete').val('');
            }
        });
    });
</script>
