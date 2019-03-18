<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<div class="row flex-layout divider">
    <h2 class="box-title flex-left">Letter contact rules</h2>
    <?=\CHtml::submitButton('Add letter contact rule', ['class' => 'button large flex-right', 'id' => 'et_add_letter_contact_rule']); ?>
</div>

<div class="row divider">
    <form id="rulestest" class="panel">
        <table class="standard">
            <tr>
                <td class="fade">Test</td>
                <td><?= \CHtml::dropDownList('lcr_site_id', '', Site::model()->getListForCurrentInstitution('name'), ['class' => 'cols-11', 'empty' => '- Site -']) ?></td>
                <td><?= \CHtml::dropDownList('lcr_subspecialty_id', '', CHtml::listData(Subspecialty::model()->findAllByCurrentSpecialty(), 'id', 'name'), ['class' => 'cols-11', 'empty' => '- Subspecialty -']) ?></td>
                <td><?= \CHtml::dropDownList('lcr_firm_id', '', [], ['empty' => '- ' . Firm::contextLabel() . ' -', 'class' => 'cols-11']) ?></td>
                <td><?= \CHtml::dropDownList('lcr_theatre_id', '', [], ['empty' => '- Theatre -', 'class' => 'cols-11']) ?></td>
            </tr>
        </table>
    </form>
</div>

<div id="nomatch" class="alert-box alert hide">No match</div>

<form id="rules" class="panel">
    <?php
    $this->widget('CTreeView', array(
        'data' => $data,
    )) ?>
</form>
