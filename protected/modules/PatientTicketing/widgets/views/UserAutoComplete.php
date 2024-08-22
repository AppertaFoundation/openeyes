<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<tbody data-formName="<?= $this->form_name ?>">
<tr>
    <td><?=$this->label;?>:</td>
    <td>
        <?php $this->widget('application.widgets.AutoCompleteSearch', [
            'field_name' => "oe-{$this->form_name}-patientticketing",
            'hidden' => true
        ]);
        ?>
        <ul id="<?= $this->form_name ?>-user-select" class="oe-multi-select inline" data-formname="<?=$this->form_name;?>_id">
            <?php if ($this->user) :?>
                <li>
                    <?=$this->user->fullName;?><i class="oe-i remove-circle small-icon pad-left"></i>
                    <input type="hidden" name="<?=$this->form_name;?>_id" value="<?=$this->user->id;?>">
                </li>
            <?php endif;?>
        </ul>
        <?php if (!$this->is_template):?>
        <script>
            ready(() => {
                OpenEyes.UI.AutoCompleteSearch.init({
                    input: $('#<?="oe-{$this->form_name}-patientticketing"?>'),
                    url: '/user/autocomplete',
                    onSelect: function () {
                        const response = OpenEyes.UI.AutoCompleteSearch.getResponse();
                        const $ul = document.getElementById('<?= $this->form_name ?>-user-select');
                        $ul.innerHTML = '';
                        $li = `<li>
                            ${response.name}<i class="oe-i remove-circle small-icon pad-left"></i>
                            <input type="hidden" name="${$ul.dataset.formname}" value="${response.id}">
                        </li>`;
                        $ul.insertAdjacentHTML('beforeend', $li);
                    }
                });

                const $ul = document.getElementById('<?= $this->form_name ?>-user-select');
                OpenEyes.UI.DOM.addEventListener($ul, 'click', '.remove-circle', function(e) {
                    e.target.closest('li').remove();
                });
            });
        </script>
        <?php endif; ?>
    </td>
</tr>
</tbody>

