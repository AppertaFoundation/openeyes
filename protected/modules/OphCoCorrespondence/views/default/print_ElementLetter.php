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
<?php if (!@$no_header) {?>
	<header>
	<?php 
        $ccString = "";
        $toAddress = "";
        
        if($element->document_instance && $element->document_instance[0]->document_target) {
            
            foreach ($element->document_instance as $instance) {
                foreach ($instance->document_target as $target) {
                    if($target->ToCc == 'To'){
                        $toAddress = $target->contact_name . "\n" . $target->address;
                    } else {

                        $contact_type = $target->contact_type != 'GP' ? ucfirst(strtolower($target->contact_type)) : $target->contact_type;
                        $ccString .= "CC: " . $contact_type . ": " . $target->contact_name . ", " . $element->renderSourceAddress($target->address)."<br/>";
                    }
                }
            }
        }else
        {
            $toAddress = $element->address;
            foreach (explode("\n", trim($element->cc)) as $line) {
                if (trim($line)) {
                    $ccString .= "CC: " . str_replace(';', ',', $line)."<br/>";
                }
            }
        }

        $this->renderPartial('letter_start', array(
            'toAddress' => isset($letter_address) ? $letter_address : $toAddress, // defaut address is coming from the 'To'
            'patient' => $this->patient,
            'date' => $element->date,
            'clinicDate' => $element->clinic_date,
            'element' => $element,
        ))?>
	</header>

<?php $this->renderPartial('reply_address', array(
        'site' => $element->site,
        'is_internal_referral' => $element->isInternalReferral(),
))?>

<?php }?>
<p class="accessible">
	<?php echo $element->renderIntroduction()?>
</p>
<p class="accessible"><strong><?php if ($element->re) {?>Re: <?php echo preg_replace("/\, DOB\:|DOB\:/", "<br />\nDOB:", CHtml::encode($element->re))?>
<?php } else {?>Hosp No: <?php echo $element->event->episode->patient->hos_num?>, NHS No: <?php echo $element->event->episode->patient->nhsnum?> <?php }?></strong></p>

<p class="accessible">
<?php echo $element->renderBody() ?>
</p>
<br/>
<p class="accessible" nobr="true">
	<?php echo $element->renderFooter() ?>
</p>

<p nobr="true">
<?php 
    echo ($toAddress ? ('To: ' . $element->renderSourceAddress($toAddress) . '<br/>' ) : '');
    echo ($ccString ? $ccString : '');
    ?>

<?php if ($element->enclosures) {?>
<?php
    foreach ($element->enclosures as $enclosure) {?>
		<br/>Enc: <?php echo $enclosure->content?>
	<?php }?>

<?php }?>
</p>
