<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<h1><?=$worklist->name?></h1>
<div class="cols-12 column">
    <?php
    if (!$worklist_patients->totalItemCount > 0) {?>
        <div class="alert-box">
            No patients in this worklist.
        </div>
        <?php

    } else {
        $core_api = new CoreAPI();
        $cols = array(
            array(
                'id' => 'hos_num',
                'class' => 'CDataColumn',
                'header' => 'Hospital No.',
                'value' => '$data->patient->hos_num',
            ),
            array(
                'id' => 'patient_name',
                'class' => 'CLinkColumn',
                'header' => 'Name',
                'urlExpression' => function($data) use ($core_api) {
                    return $core_api->generateEpisodeLink($data->patient, ['worklist_patient_id' => $data->id]);
                },
                'labelExpression' => '$data->patient->getHSCICName()',
            ),
            array(
                'id' => 'gender',
                'class' => 'CDataColumn',
                'header' => 'Gender',
                'value' => '$data->patient->genderString',
            ),
            array(
                'id' => 'dob',
                'class' => 'CDataColumn',
                'header' => 'DOB',
                'value' => 'Helper::convertMySQL2NHS($data->patient->dob)',
                'htmlOptions' => array('class' => 'date'),
            ),
        );
        if ($worklist->scheduled) {
            array_unshift($cols, array(
                'id' => 'time',
                'class' => 'CDataColumn',
                'header' => 'Time',
                'value' => '$data->scheduledtime',
            ));
        }

        foreach ($worklist->displayed_mapping_attributes as $attr) {
            $cols[] = array(
                'id' => "{$worklist->id}-attr-{$attr->id}",
                'class' => 'CDataColumn',
                'header' => $attr->name,
                'value' => function ($data) use ($attr) {
                    return $data->getWorklistAttributeValue($attr);
                },
                'type' => 'raw',
            );
        }

        $this->widget('zii.widgets.grid.CGridView', array(
            'itemsCssClass' => 'standard',
            'dataProvider' => $worklist_patients,
            'htmlOptions' => array('id' => "worklist-table-{$worklist->id}", 'style' => 'padding: 0px;'),
            'summaryText' => '<h3><small> {start}-{end} of {count} </small></h3>',
            'template' => '{pager}{items}{summary}',
            'columns' => $cols,
        ));
    } ?>
</div>