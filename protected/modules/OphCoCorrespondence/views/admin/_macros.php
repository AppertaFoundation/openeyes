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
if (!empty($macros)) {
    foreach ($macros as $i => $macro) {?>
		<tr class="clickable" data-id="<?php echo $macro->id?>" data-uri="OphCoCorrespondence/admin/editMacro/<?php echo $macro->id?>">
			<td><input type="checkbox" name="macros[]" value="<?php echo $macro->id?>" /></td>
			<td><?php echo $macro->id?></td>
			<td>
				<?php if ($macro->site) {
                    echo 'Site: '.$macro->site->name;
                } elseif ($macro->subspecialty) {
                    echo 'Subspecialty: '.$macro->subspecialty->name;
                } else {
                    echo 'Firm: '.$macro->firm->getNameAndSubspecialty();
                }?>
			</td>
			<td><?php echo $macro->name?></td>
			<td><?php echo $macro->recipient ? $macro->recipient->name : '-'?></td>
			<td><?php echo $macro->cc_patient ? 'Yes' : 'No'?></td>
			<td><?php echo $macro->cc_doctor ? 'Yes' : 'No'?></td>
			<td><?php echo $macro->cc_drss ? 'Yes' : 'No'?></td>
			<td><?php echo $macro->use_nickname ? 'Yes' : 'No'?></td>
			<td><?php echo $macro->episode_status ? $macro->episode_status->name : '-'?></td>
		</tr>
	<?php }
} else {?>
	<tr><td>No letter macros match your filters.</td></tr>
<?php }?>
