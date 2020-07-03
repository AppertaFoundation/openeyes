<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * @var $templates EmailTemplate[]
 */
?>
<div class="cols-4 column end">
    <?=CHtml::htmlButton('Add Email Template', array('class' => 'button small addEmailTemplate')) ?>
</div>

<form id="admin_email_templates">
    <input type="hidden" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken ?>"/>
    <input type="hidden" name="page" value="1">
    <div class="data-group">
        <hr class="divider">
        <table class="standard generic-admin sortable">
            <thead>
                <tr>
                    <th><input type="checkbox" name="selectall" id="selectall"/></th>
                    <th>Recipient Type</th>
                    <th>Institution</th>
                    <th>Site</th>
                    <th>Title</th>
                    <th>Subject</th>
                </tr>
            </thead>
            <tbody>
            <?php
            foreach ($templates as $template) {
                $recipient_type_display = null;
                switch ($template->recipient_type) {
                    case 'OPTOMETRIST':
                        $recipient_type_display = 'Optometrist';
                        break;
                    case 'PATIENT':
                        $recipient_type_display = 'Patient';
                        break;
                    case 'DRSS':
                        $recipient_type_display = 'Diabetic Retinopathy Screening Service';
                        break;
                    case 'INTERNALREFERRAL':
                        $recipient_type_display = 'Internal Referral';
                        break;
                    case 'GP':
                        $recipient_type_display = 'General Practitioner';
                        break;
                    case 'OTHER':
                        $recipient_type_display = 'Other';
                        break;
                    default:
                        $recipient_type_display = $template->recipient_type;
                        break;
                }?>
                <tr
                    class="clickable" data-key="<?php echo $template->id ?>"
                    data-uri="OphCoCorrespondence/admin/editEmailTemplate/<?= $template->id ?>">
                    <td><input type="checkbox" name="templates[]" value="<?php echo $template->id?>" /></td>
                    <td><?= $recipient_type_display ?></td>
                    <td><?= isset($template->institution) ? $template->institution->name : 'None' ?></td>
                    <td><?= isset($template->site) ? $template->site->name : 'None' ?></td>
                    <td><?= $template->title ?></td>
                    <td><?= $template->subject ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</form>
<br>

<div class="cols-4 column end">
    <?= CHtml::htmlButton('Delete Email Templates', array('class' => 'button large deleteEmailTemplates')) ?>
</div>

