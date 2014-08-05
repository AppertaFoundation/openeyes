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
<section class="sub-element">
	<header class="sub-element-header">
		<h3 class="sub-element-title"><?php echo $element->elementType->name?></h3>
	</header>
	<div class="row field-row">
		<div class="large-3 column">
		</div>
		<div class="large-9 column">
			<table>
				<thead>
					<tr>
						<th>Date</th>
						<th>Investigator</th>
						<th>Study</th>
						<th>Volume</th>
					</tr>
				</thead>
				<tbody>
					<?php if ($element->transactions) {?>
						<?php foreach ($element->transactions as $transaction) {?>
							<tr>
								<td><?php echo $transaction->NHSDate('date')?></td>
								<td><?php echo $transaction->investigator->name?></td>
								<td><?php echo $transaction->study->name?></td>
								<td><?php echo $transaction->volume?></td>
							</tr>
						<?php }?>
					<?php }else{?>
						<tr>
							<td colspan="4">
								No tests have been logged for this DNA.
							</td>
						</tr>
					<?php }?>
				</tbody>
			</table>
		</div>
	</div>
</section>
