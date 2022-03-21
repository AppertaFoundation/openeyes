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

<div class="admin box">
    <h2>Patients</h2>

    <div class="search-form">
        <?php $this->renderPartial('_list_search', array(
            'model' => $model,
        )); ?>
    </div><!-- search-form -->

    <?php
    $institution = Institution::model()->getCurrent();
    $selected_site_id = Yii::app()->session['selected_site_id'];

    $primary_identifier_prompt = PatientIdentifierHelper::getIdentifierDefaultPromptForInstitution(Yii::app()->params['display_primary_number_usage_code'], $institution->id, $selected_site_id);

    $dataProvider = $model->search();
    $item_count = GeneticsPatient::model()->count();

    //we do not display any results until the user click on the search button - and post his/her query
    if (!Yii::app()->request->getQuery('GeneticsPatient')) {
        $criteria = $dataProvider->getCriteria();
        $criteria->addCondition("1 != 1");
        $dataProvider->setCriteria($criteria);
    }

    $this->widget('zii.widgets.grid.CGridView', array(
        'id' => 'geneticspatient-list-view',
        'dataProvider' => $dataProvider,
        'template' => '{items}{summary}{pager}',
        'summaryCssClass' => 'left table-summary',
        'itemsCssClass' => 'standard',
        'summaryText' => 'Showing {start} to {end} of {count}',
        'pager' => array(
            'header' => '',
            'selectedPageCssClass' => 'current',
            'htmlOptions' => array('class' => 'pagination right'),
        ),
        'emptyText' => (!empty($_GET) ? 'No result found - ' : '') . 'Total of ' . $item_count . ' items',
        "emptyTagName" => 'span',

        //click on a row - only one row can be selected
        'selectableRows' => 1,

        //here we say what should happen when a row selected
        'selectionChanged' => 'function(id){ location.href = "' . $this->createUrl('view') . '/id/"+$.fn.yiiGridView.getSelection(id);}',

        'rowCssClass' => ['clickable'],

        'columns' => array(
            array(
                'name' => 'id',
                'htmlOptions' => array('width' => '70px'),
            ),
            array(
                'header' => 'Family Id',
                'htmlOptions' => array('width' => '70px'),
                'type' => 'raw',
                'value' => function ($data) {

                    $family_ids = '';
                    foreach ($data->pedigrees as $pedigrees) {
                        $family_ids .= $family_ids ? '<br>' : '';
                        $family_ids .= CHtml::link($pedigrees->id, '/Genetics/pedigree/view/' . $pedigrees->id);
                    }

                    return $family_ids;
                },
            ),

            array(
                'id' => 'hos_num',
                'header' => $primary_identifier_prompt,
                'htmlOptions' => array('width' => '70px'),
                'value' => function ($data) {
                    $institution = Institution::model()->getCurrent();
                    $selected_site_id = Yii::app()->session['selected_site_id'];
                    $primary_identifier = PatientIdentifierHelper::getIdentifierForPatient(Yii::app()->params['display_primary_number_usage_code'], $data->patient->id, $institution->id, $selected_site_id);
                    $patient_identifier_widget = $this->widget('application.widgets.PatientIdentifiers', ['patient' => $data->patient, 'show_all' => true, 'tooltip_size' => 'small'], true);
                    return PatientIdentifierHelper::getIdentifierValue($primary_identifier) . $patient_identifier_widget;
                },
                'type' => 'raw'
            ),
            array(
                'name' => 'patient.firstName',
                'htmlOptions' => array('width' => '60x'),
                'value' => '$data->patient->first_name'
            ),
            array(
                'name' => 'patient.lastName',
                'htmlOptions' => array('width' => '60px'),
                'value' => '$data->patient->last_name'
            ),
            array(
                'name' => 'patient.contact.maiden_name',
                'htmlOptions' => array('width' => '60px'),
                'value' => '$data->patient->contact->maiden_name'
            ),


            array(
                'name' => 'patient.dob',
                'value' => function ($data) {
                    $date = new DateTime($data->patient->dob);
                    return $data->patient->dob ? $date->format("j M Y") : null;
                },
                'htmlOptions' => array('width' => '85px'),
            ),

            array(
                'name' => 'diagnoses',
                'value' => function ($data) {
                    return implode(', ', $data->diagnoses);
                },
                'htmlOptions' => array('width' => '200px'),
            ),
            array(
                'header' => 'Affected status',
                'type' => 'raw',
                'value' => function ($model) {
                    if ($model->pedigrees) {
                        $html = '';
                        foreach ($model->pedigrees as $pedigree) {
                            $html .= $html ? '<br>' : '';
                            $gene = isset($pedigree->gene) ? ' (Gene: ' . $pedigree->gene->name . ')' : '';
                            $html .= '<a href="/Genetics/pedigree/view/' . $pedigree->id . '">' . $pedigree->id . $gene . '</a>';
                            $html .= " - " . $model->statusForPedigree($pedigree->id);
                        }
                    } else {
                        $html = 'None';
                    }
                    return $html;
                },
                'htmlOptions' => array('width' => '150px'),
            ),


        ),
    ));
    ?>

</div>
