<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * @var \OEModule\PatientTicketing\services\PatientTicketing_QueueSetService $qs_svc
 * @var \OEModule\PatientTicketing\services\PatientTicketing_QueueSet $queueset
 * @var \OEModule\PatientTicketing\services\PatientTicketing_QueueSetCategory $category
 * @var int $cat_id
 */
?>
<?php
$qs_svc = Yii::app()->service->getService($this::$QUEUESET_SERVICE);
?>

<div class="oe-full-header use-full-screen">
    <div class="title wordcaps">
        <b><?= $queueset ? $queueset->name : $category->name ?></b>
    </div>
    <nav class="options-right"><button class="button blue hint" id="js-virtual-clinic-btn">Change Virtual Clinic</button></nav>
</div>

<div class="oe-full-content subgrid virtual-clinic use-full-screen">
    <?php
    if ($queueset) {
        $flash_message = Yii::app()->user->getFlash('patient-ticketing-' . $queueset->getId());
        if ($flash_message) {
            ?>
            <br />
            <div class="large-12 column">
                <div class="panel">
                    <div class="alert-box with-icon success">
                        <?php echo $flash_message; ?>
                    </div>
                </div>
            </div>
            <?php
        }
    }
    ?>
    <?php $this->renderPartial('form_queueset_select', [
        'qs_svc' => $qs_svc,
        'queueset' => $queueset,
        'cat_id' => $cat_id,
        'category' => $category
    ]); ?>
    <?php $this->renderPartial('_ticketlist_search', [
        'qs_svc' => $qs_svc,
        'queueset' => $queueset,
        'cat_id' => $cat_id,
        'patients' => $patients,
    ]); ?>

    <?php $this->renderPartial('ticketlist', [
        'tickets' => $tickets,
        'queueset' => $queueset,
        'category' => $category,
        'qs_svc' => $qs_svc,
        'pagination' => $pagination
    ]); ?>
</div>

