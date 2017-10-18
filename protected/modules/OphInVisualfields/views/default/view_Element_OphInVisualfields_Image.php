<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<table class="subtleWhite normalText">
	<thead>
		<tr>
			<td>Right Eye</td>
			<td>Left Eye</td>
		</tr>
	</thead>
	<tbody>
		<tr>
			<?php
            if ($element->right_field_id) {
                $right_test = $element->right_field;
                $x = $element->right_field_id;
                ?>
				<td width="50%">
					<a class="OphInVisualfields_field_image" data-image-id="<?= $right_test->image_id ?>" href="#">
						<img src="<?php echo '/file/view/'.$right_test->cropped_image_id.'/400/img.gif'; ?>">
					</a>
				</td>
				<?php

            } else {
                ?>
				<td>&nbsp;</td>
				<?php

            }
            if ($element->left_field_id) {
                $x = $element->left_field_id;
                $left_test = $element->left_field;
                ?>
				<td width="50%">
					<a class="OphInVisualfields_field_image" data-image-id="<?= $left_test->image_id ?>" href="#">
						<img src="<?php echo '/file/view/'.$left_test->cropped_image_id.'/400/img.gif'; ?>">
					</a>
				</td>
				<?php
            } else {
                ?>
				<td>&nbsp;</td>
				<?php
            }
            ?>
		</tr>
		<tr>
			<?php
            if ($element->right_field_id) {
                ?>
				<td width="50%">Date: <?php echo $right_test->study_datetime ?></td>
				<?php
            } else {
                ?>
				<td>&nbsp;</td>
				<?php
            }
            ?>
			<?php
            if ($element->left_field_id) {
                ?>
				<td width="50%">Date: <?php echo $left_test->study_datetime ?></td>
				<?php
            } else {
                ?>
				<td>&nbsp;</td>
				<?php
            }
            ?>
		</tr>
		<tr>
			<?php
            if ($element->right_field_id) {
                ?>
				<td width="50%">Strategy: <?php echo $right_test->strategy->name ?></td>
				<?php
            } else {
                ?>
				<td>&nbsp;</td>
				<?php
            }
            ?>
			<?php
            if ($element->left_field_id) {
                ?>
				<td width="50%">Strategy: <?php echo $left_test->strategy->name ?></td>
				<?php
            } else {
                ?>
				<td>&nbsp;</td>
				<?php
            }
            ?>
		</tr>
		<tr>
			<?php
            if ($element->right_field_id) {
                ?>
				<td width="50%">Test Name: <?php echo $right_test->pattern->name ?></td>
				<?php
            } else {
                ?>
				<td>&nbsp;</td>
				<?php
            }
            ?>
			<?php
            if ($element->left_field_id) {
                ?>
				<td width="50%">Test Name: <?php echo $left_test->pattern->name ?></td>
				<?php
            } else {
                ?>
				<td>&nbsp;</td>
				<?php
            }
            ?>
		</tr>
	</tbody>
</table>
