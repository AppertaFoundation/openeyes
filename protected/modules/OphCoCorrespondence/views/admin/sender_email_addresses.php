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
 * @var $addresses SenderEmailAddresses[]
 */
?>
<div class="cols-4 column end">
    <?=CHtml::htmlButton('Add Email Address', array('class' => 'button small addEmailAddress')) ?>
</div>

<form id="sender_email_addresses">
    <input type="hidden" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken ?>"/>
    <input type="hidden" name="page" value="1">
    <div class="data-group">
        <hr class="divider">
        <table class="standard generic-admin sortable">
            <thead>
                <tr>
                    <th><input type="checkbox" name="selectall" id="selectall"/></th>
                    <th>Username</th>
                    <th>Institution</th>
                    <th>Site</th>
                    <th>Domain</th>
                </tr>
            </thead>
            <tbody>
            <?php
            foreach ($addresses as $metadata) { ?>
                <tr
                    class="clickable" data-key="<?php echo $metadata->id ?>"
                    data-uri="OphCoCorrespondence/admin/editEmailAddress/<?= $metadata->id ?>">
                    <td><input type="checkbox" name="email_addresses[]" value="<?php echo $metadata->id?>" /></td>
                    <td><?php echo $metadata->username ?></td>
                    <td><?= $metadata->institution_id ? Institution::model()->find('id = '. $metadata->institution_id )->name : "None" ?></td>
                    <td><?= $metadata->site_id ? Site::model()->find('id = '. $metadata->site_id )->name : "None" ?></td>
                    <td><?= $metadata->domain ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</form>
<br>

<div class="cols-4 column end">
    <?= CHtml::htmlButton('Delete Email Addresses', array('class' => 'button large deleteEmailAddresses')) ?>
</div>

