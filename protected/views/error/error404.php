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
	$helpdesk_phone = Yii::app()->params['helpdesk_phone'];
	$helpdesk_email = Yii::app()->params['helpdesk_email'];
	$this->layout = 'error';
?>
<h3>Page not found</h3>
<p><strong>There has been a problem trying to access OpenEyes, please
	try again in a moment</strong></p>
<p>If there continues to be a problem please contact support.</p>
<h4>Support Options</h4>
<ul>
	<li>Immediate support (8:00am to 8:00pm) - Phone <?php echo @$helpdesk_phone?></li>
	<li>Less urgent issues email <a
		href="mailto:<?php echo @$helpdesk_email?>"><?php echo @$helpdesk_email?>
	</a></li>
</ul>
<div style="color: gray;"><?php echo date('r'); ?></div>
