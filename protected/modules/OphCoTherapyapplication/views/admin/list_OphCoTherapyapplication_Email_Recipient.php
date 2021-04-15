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
<?php $this->renderPartial('//base/_messages') ?>
<div class="hidden" id="add-new-form" style="margin-bottom: 10px">
    <?php
    $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
        'id' => 'clinical-create',
        'enableAjaxValidation' => false,
        'action' => Yii::app()->createURL($this->module->getName() . '/admin/addEmailRecipient'),
    ));
    $this->endWidget();
    ?>
</div>

<div class="cols-7">
<div class="row divider">
    <h2><?php echo $title ?></h2>
</div>

<form id="admin_email_recipients">
    <table class="standard">
        <thead>
        <tr>
            <th><input type="checkbox" name="selectall" id="selectall"/></th>
            <th>Institution</th>
            <th>Site</th>
            <th>Letter types</th>
            <th>Recipient name</th>
            <th>Recipient email</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($model_list as $i => $model) { ?>
            <tr class="clickable" data-id="<?php echo $model->id ?>"
                data-uri="OphCoTherapyapplication/admin/editEmailRecipient/<?php echo $model->id ?>">
                <td><input type="checkbox" name="email_recipients[]" value="<?php echo $model->id ?>"/></td>
                <td>
                    <?php echo $model->institution ? $model->institution->name : '-' ?>
                </td>
                <td>
                    <?php echo $model->site ? $model->site->name : 'All sites' ?>
                </td>
                <td>
                    <?php echo $model->type ? $model->type->name : 'Both types' ?>
                </td>
                <td>
                    <?php echo $model->recipient_name ?>
                </td>
                <td>
                    <?php echo $model->recipient_email ?>
                </td>
            </tr>
        <?php } ?>
        </tbody>
        <tfoot class="pagination-container">
        <tr>
            <td colspan="5">
                <?= CHtml::submitButton(
                    'Add',
                    [
                        'class' => 'button large',
                        'data-uri' => '/OphCoTherapyapplication/admin/addEmailRecipient',
                    'id' => 'et_add']
                );
?>
                <?= CHtml::submitButton(
                    'Delete',
                    [
                        'class' => 'button large',
                        'data-uri' => '/OphCoTherapyapplication/admin/deleteEmailRecipients',
                        'data-object' => 'email_recipients',
                        'id' => "et_delete",
                    ]
                );
?>
            </td>
        </tr>
        </tfoot>
    </table>
</form>
<div class="row divider">
    <h2>How the rules are applied</h2>
</div>

<p>
    Rules set for a specific site are in addition to rules set for "All sites", so for example you could set a rule for
    "All sites" and then extend it for a few sites that need additional recipients.
</p>
<p>
    If any rules are found that match the current site or "All sites" AND the letter type (compliant/non-compliant),
    they are ALL used. So for example if you have three email addresses associated with St Georges and non-compliant,
    and the letter being sent is non-compliant, they will all be processed and all three recipients will receive the
    emails.
</p>
<p>
    If there are rules that match the current site but not the letter type, or no rules for the current site at all, it
    will fall back to looking for rules for "All sites" which do match the letter type. In this case all rules found for
    "All sites" which match the letter type of the letter being sent will be processed and all recipients defined in
    these rules will receive the emails.
</p>
</div>