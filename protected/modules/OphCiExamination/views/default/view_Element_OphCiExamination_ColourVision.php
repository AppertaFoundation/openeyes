<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>

<div class="element-data element-eyes row">
	<div class="element-eye right-eye column">
		<div class="data-row">
			<?php if ($element->hasRight()) {
    ?>
				<table class="element-table">
					<thead>
					<tr>
						<th>Method</th>
						<th>Result</th>
					</tr>
					</thead>
					<tbody>
					<?php foreach ($element->right_readings as $reading) {
    ?>
						<tr>
							<td><?php echo $reading->method->name ?></td>
							<td><?php echo $reading->value->name ?></td>
						</tr>
					<?php 
}
    ?>
					</tbody>
				</table>
			<?php 
} else {
    ?>
				<div class="data-value">None given</div>
			<?php 
}?>
		</div>
	</div>
	<div class="element-eye left-eye column">
		<div class="data-row">
			<?php if ($element->hasLeft()) {
    ?>
				<table class="element-table">
					<thead>
					<tr>
						<th>Method</th>
						<th>Result</th>
					</tr>
					</thead>
					<tbody>
					<?php foreach ($element->left_readings as $reading) {
    ?>
						<tr>
							<td><?php echo $reading->method->name ?></td>
							<td><?php echo $reading->value->name ?></td>
						</tr>
					<?php 
}
    ?>
					</tbody>
				</table>
			<?php 
} else {
    ?>
				<div class="data-value">None given</div>
			<?php 
}?>
		</div>
	</div>
</div>
