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

<div class="cols-7">

<div class="row divider">
    <h2>EROD rules</h2>
</div>

<form id="erod_rules">
    <table class="standard">
        <thead>
        <tr>
            <th><input type="checkbox" id="checkall" class="erod_rules"/></th>
            <th>Subspecialty</th>
            <th><?php echo Firm::contextLabel() ?>s</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $criteria = new CDbCriteria();
        $criteria->order = 'display_order asc';
        foreach (OphTrOperationbooking_Operation_EROD_Rule::model()->findAll() as $i => $erod) { ?>
            <tr class="clickable sortable" data-id="<?php echo $erod->id ?>?>"
                data-uri="OphTrOperationbooking/admin/editerodrule/<?php echo $erod->id ?>">
                <td><input type="checkbox" name="erod[]" value="<?php echo $erod->id ?>" class="erod_rules"/></td>
                <td><?php echo $erod->subspecialty->name ?></td>
                <td><?php echo $erod->firmString ?></td>
            </tr>
        <?php } ?>
        </tbody>
        <tfoot>
        <tr>
            <td colspan="3">
                <?=\CHtml::submitButton('Add', ['id' => 'et_add_erod_rule', 'class' => 'buttons large', 'name' => 'add_erod_rule']);?>
                <?=\CHtml::submitButton('Delete', ['id' => 'et_delete_erod_rule', 'class' => 'buttons large', 'name' => 'delete_erod_rule']);?>
            </td>
        </tr>
        </tfoot>
    </table>
</form>
<div class="row divider">
    <h3>How the rules are applied</h3>
</div>
<p>
    The EROD is calculated by looking for the earliest available session that has space for the procedure, and meets the
    various criteria of the procedure itself (such as consultant, anaethetist etc).
</p>
<p>
    The sessions that this criteria is applied to is filtered by the subspecialty of the operation booking episode.
</p>
<p>
    If a rule is set on the subspecialty, then the sessions that are looked in will only be the sessions of the firms
    that are selected for that subspecialty rule. <i>As a result, only one rule for any subspecialty should be
        created.</i>
</p>
<p>
    This provides the added convenience of allowing EROD to be calculated outside of the episode subspecialty by
    pointing at other subspecialties via the appropriate firms.
</p>

</div>