<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2020, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
use OEModule\PatientTicketing\models\QueueSet;
?>
<tr>
    <td><?= $queueset->getAttributeLabel($field) ?></td>
    <td>
        <label class="inline highlight">
            <?=\CHtml::radioButton(\CHtml::modelName($queueset) . "[$field]",
                $queueset->$field === QueueSet::STATUS_YES,
                ['id' => CHtml::modelName($queueset) . "_{$field}_" . QueueSet::STATUS_YES, 'value' => QueueSet::STATUS_YES, 'class' => 'Yes']
            ); ?>
            Yes
        </label>
        <label class="inline highlight">
            <?=\CHtml::radioButton(\CHtml::modelName($queueset) . "[$field]",
                $queueset->$field === QueueSet::STATUS_NO,
                ['id' => CHtml::modelName($queueset) . "_{$field}_" . QueueSet::STATUS_NO, 'value' => QueueSet::STATUS_NO, 'class' => 'No']
            ); ?>
            No
        </label>
    </td>
</tr>
