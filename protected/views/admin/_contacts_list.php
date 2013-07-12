<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

?>
<div class="curvybox white">
	<div class="admin">
		<h3 class="georgia">Contacts</h3>
		<div class="pagination">
			<?php echo $this->renderPartial('_pagination',array(
				'prefix' => '/admin/contacts/',
				'page' => $contacts['page'],
				'pages' => $contacts['pages'],
				'url' => '/admin/contacts?q='.@$_GET['q'].'&page={{PAGE}}',
			))?>
		</div>
		<div>
			<form id="admin_contacts">
				<ul class="grid reduceheight">
					<li class="header">
						<span class="column_id">ID</span>
						<span class="column_title">Title</span>
						<span class="column_first_name">First name</span>
						<span class="column_last_name">Last name</span>
						<span class="column_qualifications">Qualifications</span>
						<span class="column_label">Label</span>
					</li>
					<?php
					foreach ($contacts['contacts'] as $i => $contact) {?>
						<li class="<?php if ($i%2 == 0) {?>even<?php } else {?>odd<?php }?>" data-attr-id="<?php echo $contact->id?>">
							<span class="column_id"><?php echo $contact->id?></span>
							<span class="column_title"><?php echo $contact->title?>&nbsp;</span>
							<span class="column_first_name"><?php echo $contact->first_name?>&nbsp;</span>
							<span class="column_last_name"><?php echo $contact->last_name?>&nbsp;</span>
							<span class="column_qualifications"><?php echo $contact->qualifications?>&nbsp;</span>
							<span class="column_label"><?php echo $contact->label ? $contact->label->name : 'None'?>&nbsp;</span>
						</li>
					<?php }?>
				</ul>
			</form>
		</div>
	</div>
</div>
