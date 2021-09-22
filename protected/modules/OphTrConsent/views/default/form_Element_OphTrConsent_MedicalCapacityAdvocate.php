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

if (!$element->instructed_id) {
    $default_instructed_id = OphTrConsent_Medical_Capacity_Advocate_Instructed::model()->getDefault();
} else {
    $default_instructed_id = $element->instructed_id;
}

?>
<div class="element-fields full-width">
    <div class="flex-t">
		<div class="cols-6">
			<table class="cols-full last-left">
				<colgroup><col class="cols-8"><col class="cols-4"></colgroup">				<tbody>
		            <tr>
						<td>
                            <?= CHtml::encode($element->getAttributeLabel("instructed_id"))?>
						</td>
						<td>
							<fieldset>
                                <?= $form->radioButtons(
                                    $element,
                                    'instructed_id',
                                    'OphTrConsent_Medical_Capacity_Advocate_Instructed',
                                    $default_instructed_id,
                                    false,
                                    false,
                                    false,
                                    false,
                                    array('nowrapper' => true),
                                    null
                                ) ?>
                            </fieldset>
                        </td>
					</tr>
					
				</tbody>
			</table>
		</div>
		<div class="cols-5">
			
			<div class="row">
                <?= CHtml::encode($element->getAttributeLabel("outcome_decision"))?>
			</div>
			<div class="row">
                <?php echo $form->textArea(
                    $element,
                    "outcome_decision",
                    array('nowrapper' => true),
                    false,
                    array(
                        'class' => 'cols-full',
                        'rows' => '1',
                        'placeholder' => "Outcome decision or comments"
                    )
                ); ?>
			</div>
			
		</div>	
	</div>
</div>
