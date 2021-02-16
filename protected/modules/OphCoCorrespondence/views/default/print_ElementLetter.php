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
<?php
/**
 * @var $element ElementLetter
 * @var $no_header bool
 */

$toAddressContactType = $element->getToAddressContactType();
$isToAddressDocumentOutputEmail = $element->isToAddressDocumentOutputEmail();
if (is_null($contact_type)) {
    $contact_type = $toAddressContactType;
}
if ($contact_type === "PATIENT") {
    $exam_api = Yii::app()->moduleAPI->get('OphCiExamination');
    $examination_communication_preferences = $exam_api->getElementFromLatestVisibleEvent('OEModule\OphCiExamination\models\Element_OphCiExamination_CommunicationPreferences', $this->patient);
    if ($examination_communication_preferences) {
        $large_letters = $examination_communication_preferences->correspondence_in_large_letters === '1';
    }
}

if (!@$no_header) { ?>
    <header class="print-header" style="margin-bottom: 0;">
        <?php
        $ccString = $element->getCCString();
        $toAddress = $element->getToAddress();

        $this->renderPartial('letter_start', array(
            'toAddress' => isset($letter_address) ? $letter_address : $toAddress, // default address is coming from the 'To'
            'patient' => $this->patient,
            'date' => $element->date,
            'clinicDate' => strtotime($element->clinic_date),
            'element' => $element,
        )) ?>
    </header>

    <?php $this->renderPartial('reply_address', array(
        'site' => $element->site,
        'is_internal_referral' => $element->isInternalReferral(),
    )) ?>

<?php } ?>

<div <?= isset($large_letters) && $large_letters ? 'class="impaired-vision"' : '' ?>>
    <p class="accessible">
        <?php echo $element->renderIntroduction() ?>
    </p>
    <p class="accessible">
        <strong>
            <?php if ($toAddressContactType !== "PATIENT" && $element->re) {
                echo 'Re: ' . preg_replace("/\, DOB\:|DOB\:/", "<br/>\nDOB:", CHtml::encode($element->re));
            } elseif ($contact_type === "PATIENT") {
                if (Yii::app()->params['nhs_num_private'] == true) {
                    echo Yii::app()->params['hos_num_label'] . ': ' . $element->event->episode->patient->hos_num;
                } else {
                    echo Yii::app()->params['hos_num_label'] . ': ' . $element->event->episode->patient->hos_num . ', ' . \SettingMetadata::model()->getSetting('nhs_num_label') . ': ' . $element->event->episode->patient->nhsnum;
                }
            } else {
                echo 'Re: ' . preg_replace("/\, DOB\:|DOB\:/", "<br/>\nDOB:", CHtml::encode($element->calculateRe($this->patient)));
            } ?>
        </strong>
    </p>
    <p class="accessible">
        <?php echo $element->renderBody() ?>
    </p>
    <br/>
    <p class="accessible" nobr="true">
        <?php echo $element->renderFooter() ?>
    </p>

    <div class="spacer"></div>
    <h5>
        <?php
        echo($toAddress ? ('To: ' . (isset($toAddressContactType) ? $toAddressContactType . ' : ' : '') . $element->renderSourceAddress($toAddress) . ($isToAddressDocumentOutputEmail ? ' (Sent by Email)' : '') . '<br/>') : '');
        echo($ccString ? $ccString : ''); ?>
    </h5>
    <p nobr="true">
        <?php if ($element->enclosures) { ?>
            <?php
            foreach ($element->enclosures as $enclosure) { ?>
                <br/>Enc: <?php echo $enclosure->content ?>
            <?php } ?>

        <?php } ?>

        <?php
        $associated_content = EventAssociatedContent::model()
            ->with('initAssociatedContent')
            ->findAllByAttributes(
                array('parent_event_id' => $element->event->id),
                array('order' => 't.display_order asc')
            );

        if ($associated_content) { ?>
            <br>
            Attachments:
            <?php
            $attachments = array();
            foreach ($associated_content as $ac) {
                if ($ac->display_title) {
                    $attachments[] = $ac->display_title;
                } else {
                    $associated_event = Event::model()->findByPk($ac->associated_event_id);

                    $attachments[] = $associated_event->eventType->name . ' (' . Helper::convertDate2NHS($associated_event->event_date) . ')';
                }
            }
            echo implode(", ", $attachments);
        }
        ?>
    </p>
</div>