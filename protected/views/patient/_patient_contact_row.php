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
$contact = $pca->location ? $pca->location->contact : $pca->contact;
?>
<tr data-attr-pca-id="<?php echo $pca->id?>"<?php if ($pca->location) {?> data-attr-location-id="<?php echo $pca->location_id?>"<?php } if ($pca->contact) {?> data-attr-contact-id="<?php echo $pca->contact_id?>"<?php }?>>
	<td>
		<?php echo CHtml::encode($contact->fullName)?> <br />
		<?php echo CHtml::encode($contact->qualifications)?>
	</td>
	<td>
		<?php echo CHtml::encode($pca->locationText)?>
	</td>
	<td>
		<?php echo CHtml::encode($contact->label->name)?>
	</td>
	<td>
		<?php if ($this->checkAccess('OprnEditContact')) {?>
			<?php if ($pca->location) {?>
				<a class="editContact" rel="<?php echo $pca->id?>" href="#">
					Edit
				</a>
				<br/>
			<?php }?>
			<a class="removeContact small" rel="<?php echo $pca->id?>" href="#">
				Remove
			</a>
		<?php }?>
	</td>
</tr>
