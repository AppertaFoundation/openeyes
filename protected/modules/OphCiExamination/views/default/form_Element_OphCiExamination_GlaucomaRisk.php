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
<div class="sub-element-fields">
	<div class="field-row row collapse">
		<div class="large-2 column">
			<div class="field-highlight<?php if ($element->risk) {
    ?> <?php echo $element->risk->class?><?php 
}?> risk">
				<?php
                    $html_options = array('nowrapper' => true, 'empty' => '--- Please select ---');
                    $risks = OEModule\OphCiExamination\models\OphCiExamination_GlaucomaRisk_Risk::model()->findAll();
                    foreach ($risks as $option) {
                        $html_options['options'][(string) $option->id] = array(
                            'data-clinicoutcome-template-id' => $option->clinicoutcome_template_id,
                            'class' => $option->class,
                        );
                    }
                    echo $form->dropdownList($element, 'risk_id', CHtml::listData($risks, 'id', 'name'), $html_options);
                ?>
			</div>
		</div>
		<div class="large-10 column">
			<div class="postfix align">
				<a href="#" class="field-info descriptions_link">definitions</a>
			</div>
		</div>
	</div>
	<div class="field-row row glaucoma-risk-descriptions" id="<?= CHtml::modelName($element) ?>_descriptions">
		<dl>
			<?php foreach ($risks as $option) {
    ?>
				<dt class="pill <?php echo $option->class ?>">
					<a href="#" data-risk-id="<?php echo $option->id ?>">
						<?php echo $option->name ?>
					</a>
				</dt>
				<dd class="<?php echo $option->class ?>">
					<?php echo nl2br($option->description) ?>
				</dd>
			<?php 
}?>
		</dl>
	</div>
</div>
