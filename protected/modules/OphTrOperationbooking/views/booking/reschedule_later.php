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

<?php $this->beginContent('//patient/event_container', array('no_face' => true));
$institution = Institution::model()->getCurrent();
$selected_site_id = Yii::app()->session['selected_site_id'];
$primary_identifier_usage_type = SettingMetadata::model()->getSetting('display_primary_number_usage_code');
$primary_identifier = PatientIdentifierHelper::getIdentifierForPatient(
    SettingMetadata::model()->getSetting('display_primary_number_usage_code'),
    $patient->id,
    $institution->id,
    $selected_site_id
);
?>

    <div id="schedule">
        <br/>

        <section class="element">
            <section class="element-fields full-width">
        <div class="flex-layout">
            <div class="cols-2">
                <div class="field-label">
                    Patient:
                </div>
            </div>
            <div class="cols-10">
                <div class="field-value">
                    <?php echo $patient->getDisplayName() . ' (' . PatientIdentifierHelper::getIdentifierValue($primary_identifier) . ')'; ?>
                    <?php $this->widget(
                        'application.widgets.PatientIdentifiers',
                        [
                            'patient' => $this->patient,
                            'show_all' => true
                        ]
                    ); ?>
                </div>
            </div>
        </div>

        <div id="operation">
            <input type="hidden" id="booking"
                   value="<?php echo isset($operation->booking) ? $operation->booking->id : ''; ?>"/>
            <?php
            if (Yii::app()->user->hasFlash('info')) { ?>
                <div class="flash-error">
                    <?php echo Yii::app()->user->getFlash('info'); ?>
                </div>
            <?php } ?>
            <p><strong>Operation duration:</strong> <?php echo $operation->total_duration; ?> minutes</p>
            <p><strong>Current schedule:</strong></p>
            <?php $this->renderPartial('_session', array('operation' => $operation)); ?>
            <br/>
            <?php
            echo CHtml::form(array('booking/rescheduleLater/' . $operation->event_id), 'post', array('id' => 'cancelForm'));
            echo CHtml::hiddenField('booking_id', isset($operation->booking) ? $operation->booking->id : null);
            ?>

            <div class="flex-layout">
                <div class="cols-2">
                    <?= \CHtml::label('Re-schedule reason: ', 'cancellation_reason'); ?>
                </div>
                <div class="cols-10">
                    <?php
                    $listIndex = 2;
                    if (isset($operation->booking)) {
                        if (date('Y-m-d') == date('Y-m-d', strtotime($operation->booking->session->date))) {
                            $listIndex = 3;
                        }
                    }

                    echo CHtml::dropDownList(
                        'cancellation_reason',
                        @$_POST['cancellation_reason'],
                        OphTrOperationbooking_Operation_Cancellation_Reason::getReasonsByListNumber($listIndex),
                        array('empty' => 'Select a reason')
                    ); ?>
                </div>
            </div>

            <div class="flex-layout">
                <div class="cols-2">
                    <?= CHtml::label('Comments: ', 'cancellation_comment') ?>
                </div>
                <div class="cols-10">
                    <?= \CHtml::textArea(
                        'cancellation_comment',
                        @$_POST['cancellation_comment'],
                        array('rows' => 6, 'cols' => 40, 'class' => 'cols-full autosize')
                    ) ?>
                </div>
            </div>

            <div class="clear"></div>
            <button type="submit" class="warning">Confirm reschedule later</button>
            <img src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif') ?>" alt="loading..."
                 style="display: none;" class="loader"/>
            <?= CHtml::endForm(); ?>
        </div>
            </section>
        </section>

    </div>

<?php if (!empty($errors)) { ?>
    <div class="alert-box alert with-icon bottom"><p>Please fix the following input errors:</p>
        <ul>
            <?php foreach ($errors as $error) { ?>
                <li><?php echo htmlspecialchars($error) ?></li>
            <?php } ?>
        </ul>
    </div>
<?php } ?>

<?php $this->endContent(); ?>
