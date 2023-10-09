<?php
/**
 * OpenEyes.
 *
 * 
 * Copyright OpenEyes Foundation, 2017
 *
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
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
	<p>
		You haven't set your site and firm selections yet.	Doing so will restrict the site and firm dropdowns to the sites and firms that you work in.
	</p>
	<p>
		Do this now?
	</p>

	<div class="buttons">
		<button class="secondary small" type="button" id="yes">
			Yes
		</button>
		<button class="warning small" type="button" id="later" data-test="set-site-and-firm-later-button">
			Later
		</button>
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
