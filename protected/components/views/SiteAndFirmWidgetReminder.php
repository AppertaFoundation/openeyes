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
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
		'id' => 'site-and-firm-dialog',
		'options' => array(
			'title' => $this->title,
			'dialogClass' => 'dialog',
			'autoOpen' => true,
			'modal' => true,
			'draggable' => false,
			'resizable' => false,
			'width' => 450,
		),
	));
?>

<div>
	<p>
		You haven't set your site and firm selections yet.	Doing so will restrict the site and firm dropdowns to the sites and firms that you work in.
	</p>
</div>

<div style="margin-top: 1em; margin-bottom: 1.5em;">
	<p>
		Do this now?
	</p>
</div>

<div style="margin-left: 8em;">
	<button class="classy green mini cancel" type="button" id="yes" style="margin-right: 3em;"><span class="button-span button-span-green">Yes</span></button>
	<button class="classy red mini cancel" type="button" id="later"><span class="button-span button-span-red">Later</span></button>
</div>

<?php $this->endWidget()?>
<script type="text/javascript">
	$('#yes').click(function() {
		$('#site-and-firm-dialog').dialog('close');
		window.location.href = baseUrl+'/profile/firms';
	});
	$('#later').click(function() {
		$('#site-and-firm-dialog').dialog('close');
	});
</script>
