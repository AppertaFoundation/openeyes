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

$this->breadcrumbs=array(
	'Site Element Types'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List SiteElementType', 'url'=>array('index')),
	array('label'=>'Update SiteElementType', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Manage SiteElementType', 'url'=>array('admin')),
);
?>

<h1>View Site Element Type for: </h1>

<div class="view">

                <table>
                        <tr>
                                <td>Event type</td><td><?php echo $model->possibleElementType->eventType->name;?></td>
                        </tr>
                        <tr>
                                <td>Element type</td><td><?php echo $model->possibleElementType->elementType->name;?></td>
                        </tr>
                        <tr>
                                <td>Specialty</td><td><?php echo $model->specialty->name;?></td>
                        </tr>
                        <tr>
                                <td>First in episode</td><td><?php if ($model->first_in_episode) {echo 'Yes';} else {echo 'No';} ?></td>
                        </tr>
                </table>
</div>

<div class="view">
        <b><?php echo CHtml::encode($model->getAttributeLabel('id')); ?>:</b>
        <?php echo CHtml::link(CHtml::encode($model->id), array('view', 'id'=>$model->id)); ?>
        <br />

        <b><?php echo CHtml::encode($model->getAttributeLabel('possible_element_type_id')); ?>:</b>
        <?php echo CHtml::encode($model->possible_element_type_id); ?>
        <br />

        <b><?php echo CHtml::encode($model->getAttributeLabel('specialty_id')); ?>:</b>
        <?php echo CHtml::encode($model->specialty->name); ?>
        <br />

        <b><?php echo CHtml::encode($model->getAttributeLabel('view_number')); ?>:</b>
        <?php echo CHtml::encode($model->view_number); ?>
        <br />

        <b><?php echo CHtml::encode($model->getAttributeLabel('required')); ?>:</b>
        <?php if ($model->required) {echo 'Yes';} else {echo 'No';} ?>
        <br />

        <b><?php echo CHtml::encode($model->getAttributeLabel('first_in_episode')); ?>:</b>
        <?php if ($model->first_in_episode) {echo 'Yes';} else {echo 'No';} ?>
        <br />
</div>

