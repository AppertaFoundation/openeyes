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
use OEModule\PatientTicketing\models\TicketAssignOutcomeOption;
?>

<form id="filter" method="GET">
    <table class="standard">
        <tr>
            <td>Institution:</td>
            <td>
                <?php if ($this->checkAccess('admin')) : ?>
                    <?=\CHtml::dropDownList('institution_id', $institution->id, \CHtml::listData(\Institution::model()->findAll(), 'id', 'name'), [
                        'id' => 'filter_institution',

                    ]); ?>
                <?php else :?>
                    <?=$institution->name?>
                <?php endif;?>
            </td>

            <td>Queue Set:</td>
            <td>
                <?=\CHtml::dropDownList('queueset_id', $queueset_id, \CHtml::listData($this->getQueueSets((int)$institution->id), 'id', 'name'), [
                    'empty' => '-',
                    'id' => 'queueset_institution'
                ]); ?>
            </td>
            <td><button class="green hint js-filter">Filter</button></td>
        </tr>
    </table>
</form>
