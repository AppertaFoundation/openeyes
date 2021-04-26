<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<?php
    $pre_output_key = 0;
    // check if it is a GP and if the GP has a Docman or Print
    $document_output = null;
    $internalreferral_output = null;
    $print_output = null;
    $email_output = null;
    $email_delayed_output = null;
    $is_new_record = !isset($target) || $target->isNewRecord ? true : false;
if ( isset($target->document_output)) {
    foreach ($target->document_output as $output_key => $doc_output) {
        switch ($doc_output->output_type) {
            case 'Docman':
                $document_output = $doc_output;
                break;
            case 'Print':
                $print_output = $doc_output;
                break;
            case 'Internalreferral':
                $internalreferral_output = $doc_output;
                break;
            case 'Email':
                $email_output = $doc_output;
                break;
            case 'Email (Delayed)':
                $email_delayed_output = $doc_output;
                break;
            default:
                break;
        }
    }
}
?>

<?php if ($contact_type == \SettingMetadata::model()->getSetting('gp_label')) : ?>
    <?php if ($can_send_electronically) : ?>
        <div>
            <label class="inline highlight electronic-label docman">
                <?php
                $is_checked = $is_draft == 1 ? 'checked disabled' : '';

                //now, docman cannot be unchecked when the recipient is GP
                // if we want to allow users to tick/untick DocMan checkbox remove the following line
                $is_checked = 'checked disabled';
                ?>
                <input type="checkbox" value="Docman" name="DocumentTarget_<?php echo $row_index; ?>_DocumentOutput_<?php echo $pre_output_key; ?>_output_type"
                    <?php echo $is_checked; ?>> <?php echo (isset(Yii::app()->params['electronic_sending_method_label']) ? Yii::app()->params['electronic_sending_method_label'] : 'Electronic'); ?>
                <input type="hidden" value="Docman" name="DocumentTarget[<?php echo $row_index; ?>][DocumentOutput][<?php echo $pre_output_key; ?>][output_type]" >
            </label>
        </div>

        <?php if ($document_output) :?>
            <?=\CHtml::hiddenField("DocumentTarget[$row_index][DocumentOutput][" . $pre_output_key . "][id]", $document_output->id, array('class'=>'document_target_' . $row_index . '_document_output_id')); ?>

        <?php endif; ?>

        <?php $pre_output_key++; ?>

    <?php endif; ?>

<?php endif; ?>

<?php if ($contact_type == 'INTERNALREFERRAL') : ?>
    <?php
    if ( !$is_new_record ) {
        $elementLetter = ElementLetter::model()->find('event_id = '. $target->document_instance->correspondence_event_id);
        $serviceEmail = $elementLetter->toSubspecialty ? $elementLetter->toSubspecialty->getSubspecialtyEmail() : null;
        $contextEmail = $elementLetter->toFirm ? $elementLetter->toFirm->getContextEmail() : null;
    } else {
        $serviceEmail = isset($_POST['ElementLetter']['to_subspecialty_id']) ?
            ( Subspecialty::model()->findByPk($_POST['ElementLetter']['to_subspecialty_id']) ?
                Subspecialty::model()->findByPk($_POST['ElementLetter']['to_subspecialty_id'])->getSubspecialtyEmail() : null ) : null ;
        $contextEmail = isset($_POST['ElementLetter']['to_firm_id']) ?
            ( Firm::model()->findByPk($_POST['ElementLetter']['to_firm_id']) ?
                Firm::model()->findByPk($_POST['ElementLetter']['to_firm_id'])->getContextEmail() : null ): null ;
    }
    if ($serviceEmail && !$contextEmail ) {
        // Only Service is selected and email exists for the service
        $label = 'Email: ' . $serviceEmail;
        $value = 'Email';
        $internalreferral_output = $email_output;
    } elseif ($contextEmail) {
        // Both Service and context are selected and email exists for the context.
        $label = 'Email: ' . $contextEmail;
        $value = 'Email';
        $internalreferral_output = $email_output;
    } else {
        $label = ElementLetter::model()->getInternalReferralSettings('internal_referral_method_label', 'Electronic');
        $value = 'Internalreferral';
    }
    ?>
    <div>
        <label class="inline highlight electronic-label internal-referral">
            <?php
            //now, WinDip cannot be unchecked
            // if we want to allow users to tick/untick the checkbox remove the following line
            $is_checked = 'checked disabled';
            ?>
            <input type="checkbox" value="<?php echo $value; ?>" name="DocumentTarget_<?php echo $row_index; ?>_DocumentOutput_<?php echo $pre_output_key; ?>_output_type" <?php echo $is_checked; ?>>
            <span><?php echo $label; ?></span>
            <input type="hidden" value="<?php echo $value; ?>" name="DocumentTarget[<?php echo $row_index; ?>][DocumentOutput][<?php echo $pre_output_key; ?>][output_type]" >
        </label>
    </div>
    <?php if ($internalreferral_output) :?>
        <?=\CHtml::hiddenField("DocumentTarget[$row_index][DocumentOutput][" . $pre_output_key . "][id]", $internalreferral_output->id, array('class'=>'document_target_' . $row_index . '_document_output_id')); ?>

    <?php endif; ?>

    <?php $pre_output_key++; ?>

<?php endif; ?>

<div>
    <label class="inline highlight">
        <?php
        $is_checked = ($print_output || $is_new_record) ? ($email ? '' : 'checked') : '';
        $is_post_checked = isset($_POST['DocumentTarget'][$row_index]['DocumentOutput'][$pre_output_key]['output_type']);
        if ( $contact_type == \SettingMetadata::model()->getSetting('gp_label') || $contact_type == 'INTERNALREFERRAL') {
            $is_checked = $is_post_checked ? 'checked' : ($print_output ? 'checked' : '');
        } else {
            $is_checked = (Yii::app()->request->isPostRequest && !$is_post_checked) ? '' : $is_checked;
        }
        ?>

        <?php
        if ( isset(Yii::app()->params['OphCoCorrespondence_event_actions']['create']['saveprint']) && Yii::app()->params['OphCoCorrespondence_event_actions']['create']['saveprint'] ) : ?>
            <input type="checkbox" value="Print" name="DocumentTarget[<?php echo $row_index; ?>][DocumentOutput][<?php echo $pre_output_key; ?>][output_type]" <?php echo $is_checked?>>  Print
        <?php endif; ?>
    </label>
</div>
<?php if ($print_output) : ?>
    <?=\CHtml::hiddenField("DocumentTarget[$row_index][DocumentOutput][" . $pre_output_key . "][id]", $print_output->id, array('class'=>'document_target_' . $row_index . '_document_output_id')); ?>

<?php endif; ?>

<?php $pre_output_key++; ?>

<?php if ( $contact_type != 'INTERNALREFERRAL' && Yii::app()->params['send_email_immediately'] === "on" ) : ?>
<div>
    <label class="inline highlight">
        <?php
        $is_checked = null;
        $is_post_checked = isset($_POST['DocumentTarget'][$row_index]['DocumentOutput'][$pre_output_key]['output_type']);
        if ($contact_type === "PATIENT") {
            $patient = $this->patient ?? Patient::model()->findByPk($patient_id);
            $exam_api = Yii::app()->moduleAPI->get('OphCiExamination');
            $examination_communication_preferences = $exam_api->getElementFromLatestVisibleEvent('OEModule\OphCiExamination\models\Element_OphCiExamination_CommunicationPreferences', $patient);
            // check if the communication preferences element exists
            if ($examination_communication_preferences) {
                if ($examination_communication_preferences->agrees_to_insecure_email_correspondence === '1') {
                    if ($is_new_record || $email_output) {
                        $is_checked = ($email ? 'checked' : '');
                    } else {
                        $is_checked = ($email ? 'checked' : '');
                    }
                } else {
                    $is_checked = 'disabled';
                }
                if ($is_checked === 'disabled') {
                    $emailWarningElement = "<i class='oe-i info small pad js-has-tooltip' data-tooltip-content='Please note this patient has opted out of receiving email correspondence.'></i>";
                }
            } else {
                $is_checked = 'disabled';
                if ($is_checked === 'disabled') {
                    // not selected anything
                    $emailWarningElement = "<i class='oe-i info small pad js-has-tooltip' data-tooltip-content='No communication preference has been set for this patient.'></i>";
                }
            }
        } elseif ($contact_type === "GP") {
            if (!$can_send_electronically) {
                if ($is_new_record || $email_output) {
                    $is_checked = ($email ? 'checked' : '');
                } else {
                    $is_checked = ($email ? 'checked' : '');
                }
            }
        } elseif ($contact_type === "OTHER") {
            if ($is_new_record) {
                $is_checked = ($is_post_checked ? 'checked' : '');
            } elseif ($email_output) {
                $is_checked = ($email ? 'checked' : '');
            } else {
                $is_checked = ($email ? 'checked' : '');
            }
        } else {
            if ($is_new_record || $email_output) {
                $is_checked = ($email ? 'checked' : '');
            } else {
                $is_checked = ($email ? 'checked' : '');
            }
        }
        $is_checked = (Yii::app()->request->isPostRequest && !$is_post_checked) ? ($is_checked === 'disabled' ? 'disabled' : '') : $is_checked;
        ?>

        <input type="checkbox" onclick="isEmailPresent(<?php echo $row_index; ?>, '<?php echo $contact_type; ?>', this);" value="Email" name="DocumentTarget[<?php echo $row_index; ?>][DocumentOutput][<?php echo $pre_output_key; ?>][output_type]" <?php echo $is_checked?>>  Email

    </label>
    <?php if (isset($emailWarningElement)) : ?>
        <?= $emailWarningElement ?>

    <?php endif; ?>
</div>
    <?php if ($email_output) : ?>
        <?=\CHtml::hiddenField("DocumentTarget[$row_index][DocumentOutput][" . $pre_output_key . "][id]", $email_output->id, array('class'=>'document_target_' . $row_index . '_document_output_id')); ?>

    <?php endif; ?>

    <?php $pre_output_key++; ?>

<?php endif; ?>

<?php if ( Yii::app()->params['send_email_delayed'] === "on" ) : ?>
<div>
    <label class="inline highlight">
        <?php
        $is_checked = null;
        $is_post_checked = isset($_POST['DocumentTarget'][$row_index]['DocumentOutput'][$pre_output_key]['output_type']);
        if ($contact_type === "PATIENT") {
            $patient = $this->patient ?? Patient::model()->findByPk($patient_id);
            $exam_api = Yii::app()->moduleAPI->get('OphCiExamination');
            $examination_communication_preferences = $exam_api->getElementFromLatestVisibleEvent('OEModule\OphCiExamination\models\Element_OphCiExamination_CommunicationPreferences', $patient);
            // check if the communication preferences element exists
            if ($examination_communication_preferences) {
                if ($examination_communication_preferences->agrees_to_insecure_email_correspondence === '1') {
                    if ($is_new_record || $email_delayed_output) {
                        $is_checked = ($email ? 'checked' : '');
                    } else {
                        $is_checked = ($email ? 'checked' : '');
                    }
                } else {
                    $is_checked = 'disabled';
                }
                if ($is_checked === 'disabled') {
                    $emailWarningElement = "<i class='oe-i info small pad js-has-tooltip' data-tooltip-content='Please note this patient has opted out of receiving email correspondence.'></i>";
                }
            } else {
                $is_checked = 'disabled';
                if ($is_checked === 'disabled') {
                    // not selected anything
                    $emailWarningElement = "<i class='oe-i info small pad js-has-tooltip' data-tooltip-content='No communication preference has been set for this patient.'></i>";
                }
            }
        } elseif ($contact_type === "GP") {
            if (!$can_send_electronically) {
                if ($is_new_record || $email_delayed_output) {
                    $is_checked = ($email ? 'checked' : '');
                } else {
                    $is_checked = ($email ? 'checked' : '');
                }
            }
        } elseif ($contact_type === "OTHER") {
            if ($is_new_record) {
                $is_checked = ($is_post_checked ? 'checked' : '');
            } elseif ($email_delayed_output) {
                $is_checked = ($email ? 'checked' : '');
            } else {
                $is_checked = ($email ? 'checked' : '');
            }
        } else {
            if ($is_new_record || $email_delayed_output) {
                $is_checked = ($email ? 'checked' : '');
            } else {
                $is_checked = ($email ? 'checked' : '');
            }
        }
        $is_checked = (Yii::app()->request->isPostRequest && !$is_post_checked) ? ($is_checked === 'disabled' ? 'disabled' : '') : $is_checked;
        ?>

        <input type="checkbox" onclick="isEmailPresent(<?php echo $row_index; ?>, '<?php echo $contact_type; ?>', this);" value="Email (Delayed)" name="DocumentTarget[<?php echo $row_index; ?>][DocumentOutput][<?php echo $pre_output_key; ?>][output_type]" <?php echo $is_checked?>>  Email (Delayed)

    </label>
    <?php if (isset($emailWarningElement)) : ?>
        <?= $emailWarningElement ?>

    <?php endif; ?>
</div>
    <?php if ($email_delayed_output) : ?>
        <?=\CHtml::hiddenField("DocumentTarget[$row_index][DocumentOutput][" . $pre_output_key . "][id]", $email_delayed_output->id, array('class'=>'document_target_' . $row_index . '_document_output_id')); ?>

    <?php endif; ?>

<?php endif; ?>

