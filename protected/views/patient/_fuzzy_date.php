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
<?php
$label = @$label ?: 'Date';
$labelColumns = @$form ? $form->columns('label') : 'large-3 column';
$fieldColumns = @$form ? $form->columns('field') : 'large-9 column end';
if (isset($date)) {
	list ($sel_year, $sel_month, $sel_day) = explode("-", $date);
} else {
	$sel_day = $sel_month = null;
	$sel_year = date('Y');
}
?>
<fieldset class="row field-row fuzzy_date <?php echo @$class?>">
	<legend class="<?php echo $labelColumns;?>">
		<?php echo $label;?>:
	</legend>
	<div class="<?php echo $fieldColumns;?>">
		<div class="row">
			<div class="large-4 column">
				<select name="fuzzy_day">
					<option value="0">Day (optional)</option>
					<?php for ($i=1;$i<=31;$i++) {?>
						<option value="<?= $i?>"<?= ($i == $sel_day) ? " selected" : ""?>><?= $i?></option>
					<?php }?>
				</select>
			</div>
			<div class="large-4 column">
				<select name="fuzzy_month">
					<option value="0">Month (optional)</option>
				<?php foreach (array('January','February','March','April','May','June','July','August','September','October','November','December') as $i => $month) {?>
					<option value="<?= $i+1?>"<?= ($i+1 == $sel_month) ? " selected" : ""?>><?= $month?></option>
				<?php }?>
				</select>
			</div>
			<div class="large-4 column end">
				<select name="fuzzy_year">
				<?php for ($i=date('Y')-50;$i<=date('Y');$i++) {?>
					<option value="<?= $i?>"<?= ($i == $sel_year) ? " selected" : ""?>><?= $i?></option>
				<?php }?>
				</select>
			</div>
		</div>
	</div>
</fieldset>
