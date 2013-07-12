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
		<h3 class="georgia">Contact location</h3>
		<div>
			<span class="label-nofloat">Contact:</span>
			<?php echo $location->contact->fullName?>
		</div>
		<div>
			<span class="label-nofloat"><?php echo $location->site_id ? 'Site' : 'Institution'?>:</span>
			<?php echo $location->site ? $location->site->name : $location->institution->name?>
		</div>
	</div>
</div>
<div class="curvybox white contactPatients">
	<div class="admin">
		<h3 class="georgia">Patients</h3>
		<form id="admin_contact_patients">
			<ul class="grid reduceheight">
				<li class="header">
					<span class="column_hos_num">Hos num</span>
					<span class="column_title">Title</span>
					<span class="column_first_name">First name</span>
					<span class="column_last_name">Last name</span>
				</li>
				<?php
				foreach ($location->patients as $i => $patient) {?>
					<li class="<?php if ($i%2 == 0) {?>even<?php } else {?>odd<?php }?>" data-attr-id="<?php echo $patient->id?>">
						<span class="column_hos_num"><?php echo $patient->hos_num?>&nbsp;</span>
						<span class="column_title"><?php echo $patient->title?>&nbsp;</span>
						<span class="column_first_name"><?php echo $patient->first_name?>&nbsp;</span>
						<span class="column_last_name"><?php echo $patient->last_name?>&nbsp;</span>
					</li>
				<?php }?>
			</ul>
		</form>
	</div>
</div>
<script type="text/javascript">
	$('li.even, li.odd').click(function(e) {
		e.preventDefault();
		window.location.href = baseUrl+'/patient/view/'+$(this).attr('data-attr-id');
	});
</script>
