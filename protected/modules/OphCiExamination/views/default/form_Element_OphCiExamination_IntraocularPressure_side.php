<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

use OEModule\OphCiExamination\models;

?>
	<table id="<?= CHtml::modelName($element) . "_readings_" . $side ?>"<?php if (!$element->{"{$side}_values"}) {
    echo ' class="hidden"';
} ?>>
		<thead>
			<tr>
				<th width="76">Time</th>
				<th width="64">mm Hg</th>
				<?php if ($element->getSetting('show_instruments')): ?>
					 <th>Instrument</th>
				<?php endif ?>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php
                foreach ($element->{"{$side}_values"} as $index => $value) {
                    $this->renderPartial(
                        "{$element->form_view}_reading",
                        array(
                            "element" => $element,
                            "form" => $form,
                            "side" => $side,
                            "index" => $index,
                            "time" => substr($value->reading_time, 0, 5),
                            "value" => $value,
                        )
                    );
                }
            ?>
		</tbody>
	</table>
	<div class="field-row">
		<button type="button" id="<?= CHtml::modelName($element) . "_add_" . $side ?>" class="button small secondary">Add</button>
	</div>
	<div class="field-row">
		<?= $form->textArea($element, "{$side}_comments", array('nowrapper' => true)) ?>
	</div>
	<script type="text/template" id="<?= CHtml::modelName($element) . "_reading_template_" . $side ?>" class="hidden">
		<?php
            $this->renderPartial(
                "{$element->form_view}_reading",
                array(
                    "element" => $element,
                    "form" => $form,
                    "side" => $side,
                    "index" => "{{index}}",
                    "time" => "{{time}}",
                    "value" => new models\OphCiExamination_IntraocularPressure_Value,
                )
            );
        ?>
	</script>


