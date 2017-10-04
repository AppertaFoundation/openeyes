<?php /**
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
$lasers = OphTrLaser_Site_Laser::model()->activeOrPk($element->laser_id)->with(array('site'))
    ->findAll(array('order' => 'site.short_name asc, t.name asc'));

$sites = array();
$site_ids = array();
$laser_options = array();

foreach ($lasers as $laser) {
    if (!in_array($laser->site_id, $site_ids)) {
        $sites[] = $laser->site;
        $site_ids[] = $laser->site_id;
    }
    if ($element->site_id && $laser->site_id == $element->site_id) {
        $laser_options[] = $laser;
    }
}
?>
<div class="element-fields">
	<?php echo $form->dropDownList($element, 'site_id', CHtml::listData($sites, 'id', 'short_name'), array('empty' => '- Please select -'), false, array('label' => 2, 'field' => 3))?>
	<?php echo $form->dropDownList($element, 'laser_id', CHtml::listData($laser_options, 'id', 'name'), array('empty' => '- Please select -'), false, array('label' => 2, 'field' => 3))?>
	<div class="field-row">
		<div class="field-info">
			<em>Please select a site to see the list of available lasers.</em>
		</div>
	</div>
	<?php echo $form->dropDownList($element, 'operator_id', CHtml::listData($element->surgeons, 'id', 'ReversedFullName'), array('empty' => '- Please select -'), false, array('label' => 2, 'field' => 3))?>
</div>
