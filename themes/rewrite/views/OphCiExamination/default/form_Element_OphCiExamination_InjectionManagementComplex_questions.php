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
?>

<div id="<?php echo get_class($element)?>_<?php echo $side?>_Questions">
	<?php
	$name_stub = get_class($element) . '[' . $side . '_Answer]';
	foreach ($questions as $question) {
		?>
		<div class="eventDetail">
			<div class="label">
				<?php echo $question->question ?>
			</div>
			<?php
			$name = $name_stub . '[' . $question->id . ']';
			$value = $element->getQuestionAnswer($side, $question->id);
			// update with POST values if available
			if (isset($_POST[get_class($element)][$side . '_Answer'][$question->id])) {
				$value = $_POST[get_class($element)][$side . '_Answer'][$question->id];
			}
			?>
			<div class="data">
			<span class="group">
			<?php
			echo CHtml::radioButton($name, $value, array('id' => get_class($element) . '_' . $side . '_Answer_' . $question->id . '_1', 'value' => 1));
			?>
			<label>Yes</label>
			</span>

			<span class="group">
			<?php
			echo CHtml::radioButton($name, (!is_null($value) && !$value), array('id' => get_class($element) . '_' . $side . '_Answer_' . $question->id . '_0', 'value' => 0));
			?>
			<label>No</label>
			</span>
			</div>
		</div>
		<?php
	}?>
</div>
