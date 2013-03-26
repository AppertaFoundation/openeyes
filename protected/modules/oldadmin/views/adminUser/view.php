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

$this->breadcrumbs=array(
	'Users'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List User', 'url'=>array('index')),
	array('label'=>'Create User', 'url'=>array('create')),
	array('label'=>'Update User', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Manage User', 'url'=>array('admin')),
	array('label'=>'User Rights', 'url'=>array('rights', 'id'=>$model->id)),
);
?>

<h1>View User #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'username',
		'first_name',
		'last_name',
		'email',
		array(
			'name' => 'active',
			'value' => CHtml::encode($model->getActiveText())
		),
                array(
                        'name' => 'global_firm_rights',
                        'value' => CHtml::encode($model->getGlobalFirmRightsText())
                ),
	),
));
?>
<table class="detail-view" id="rights">
<tr class="even"><th>Rights</th><td>
<b>Services</b>
<br />
<?php
        foreach ($rights as $service) {
		if ($service['checked']) {
			echo $service['name'] . "<br />\n";
		}
	}
?>
<br />
<b>Firms</b>
<br />
<?php
	foreach ($rights as $service) {
		foreach ($service['firms'] as $firm) {
			if ($firm['checked']) {
				echo $firm['name'] . ' (' . $service['name'] . ")<br />\n";
			}
                }
        }
?>
</td></tr>
</table>
