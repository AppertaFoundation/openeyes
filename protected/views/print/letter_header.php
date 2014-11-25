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
$event = $this->event;
$event_type = $event->eventType->name;
?>
<header class="header">
	<div class="letter-logo">
		<img src="<?php echo Yii::app()->assetManager->createUrl('img/_print/letterhead_Moorfields_NHS.jpg')?>" alt="letterhead_logo" />
	</div>
	<div class="seal">
		<img src="<?php echo Yii::app()->assetManager->createUrl('img/_print/letterhead_seal.jpg')?>" alt="letterhead_seal" width="100" height="83" />
	</div>
	<div class="row">
		<div class="large-4 column patient">
			<strong><?php echo $this->patient->contact->fullName?></strong>
			<br />
			<?php echo $this->patient->getLetterAddress(array(
				'delimiter' => '<br/>',
			))?>
			<br />
			<br />
			Hospital No: <strong><?php echo $this->patient->hos_num ?></strong>
			<br />
			NHS No: <strong><?php echo $this->patient->nhsnum ?></strong>
			<br />
			DOB: <strong><?php echo Helper::convertDate2NHS($this->patient->dob) ?> (<?php echo $this->patient->getAge()?>)</strong>
		</div>
	</div>
</header>
