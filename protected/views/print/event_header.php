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
$path = Yii::app()->basePath . '/runtime/';
$yourImageUrl = Yii::app()->assetManager->publish($path);
$imageLists = scandir($path, 1);
foreach ($imageLists as $imageList) {
if(strpos($imageList,"header") !== false) {
    $headerLogo = $imageList;
}
if(strpos($imageList,"secondary") !== false)  {
    $secondaryLogo = $imageList;
        
}
}

?>
<header class="header">
	<div class="title">
        <?php if(!empty($headerLogo)){?>	
            <img src="<?php echo $yourImageUrl . "/" . $headerLogo;?>" alt="letterhead_seal" width="100" height="83"/>
                <?php } ?>

		<h1><?php echo $event_type;?></h1>
	</div>
	<div class="row">
		<!-- Patient details -->
		<div class="large-4 column patient">
			<strong><?php echo $this->patient->contact->fullName?></strong>
			<br />
			<p>
			<?php echo $this->patient->getLetterAddress(array(
				'delimiter' => '<br/>',
			))?>
			</p>
		</div>
		<div class="large-4 column firm">
			<?php if ($consultant = $this->event->episode->firm->consultant) {?>
			<p><strong><?php echo $consultant->contact->getFullName() ?></strong></p>
			<?php }?>
			<p>Service: <strong><?php echo $this->event->episode->firm->getSubspecialtyText() ?></strong></p>
			<p>Hospital No: <strong><?php echo $this->patient->hos_num ?></strong>
			<br />
			NHS No: <strong><?php echo $this->patient->nhsnum ?></strong>
			<br />
			DOB: <strong><?php echo Helper::convertDate2NHS($this->patient->dob) ?> (<?php echo $this->patient->getAge()?>)</strong>
			</p>
		</div>
		<div class="dates" style="width: 33.33333%;float: left;">
			<p><?php echo $event_type;?> Created: <strong><?php echo Helper::convertDate2NHS($this->event->created_date) ?></strong></p>
			<p><?php echo $event_type;?> Printed: <strong><?php echo Helper::convertDate2NHS(date('Y-m-d')) ?></strong></p>
		</div>
	</div>
</header>
