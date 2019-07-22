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
?>
<?php

$values = array();
$options = array();
foreach (OphTrIntravitrealinjection_LensStatus::model()->activeOrPk($element->{$side.'_lens_status_id'})->findAll() as $lens_status) {
    $values[] = $lens_status;
    $options[$lens_status->id]['data-default-distance'] = $lens_status->default_distance;
}
?>
<label for="<?php echo get_class($element).'_'.$side.'_lens_status_id';?>">
    <?=\CHtml::encode($element->getAttributeLabel($side.'_lens_status_id'));?>:
</label>
<?php echo $form->dropDownList($element, $side.'_lens_status_id', CHtml::listData($values, 'id', 'name'), array('nowrapper' => true, 'empty' => 'Select', 'options' => $options))?>