<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<?php

/**
 * @var WorklistController $this
 * @var Worklist $worklist
 */

$worklist_patients = $this->manager->getPatientsForWorklist($worklist);
$worklist_patients->pagination->pageVar = 'page' . $worklist->id;
// Get data so that pagination  works
$worklist_patients->getData();
?>
<div class="worklist-group" id="js-worklist-<?=$worklist->id?>">
<div class="worklist-summary flex-layout">
  <h2 id="worklist_<?= $worklist->id ?>"><?= $worklist->name ?></h2>
  <div class="summary">
    <?php $this->widget('LinkPager', ['pages' => $worklist_patients->getPagination()]); ?>
  </div>
</div>

<?php
if ($worklist_patients->totalItemCount <= 0) { ?>
  <div class="alert-box info">
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
            'headerHtmlOptions' => array('colgroup' => 'cols-2'),
        ),
        array(
            'id' => 'patient_name',
            'class' => 'CDataColumn',
            'header' => 'Name',
            'value' => function($data) use ($core_api) {
                return '<div class="js-worklist-url" data-url="'.$core_api->generateEpisodeLink($data->patient, ['worklist_patient_id' => $data->id]).'">'.$data->patient->getHSCICName().'</div>';
            },
            'headerHtmlOptions' => array('colgroup' => 'cols-6'),
            'type' => 'raw',
        ),
        array(
            'id' => 'gender',
            'class' => 'CDataColumn',
            'header' => 'Gender',
            'value' => '$data->patient->genderString',
            'headerHtmlOptions' => array('colgroup' => 'cols-1'),
        ),
        array(
            'id' => 'dob',
            'class' => 'CDataColumn',
            'header' => 'DOB',
            'headerHtmlOptions' => array('class' => 'date', 'colgroup' => 'cols-2'),
            'value' => function ($data) {
                return '<span class="oe-date">' . Helper::convertDate2Html(Helper::convertMySQL2NHS($data->patient->dob)) . '</span>';
            },
            'type' => 'raw',
        ),
    );
    if ($worklist->scheduled) {
        array_unshift($cols, array(
            'id' => 'time',
            'class' => 'CDataColumn',
            'header' => 'Time',
            'value' => '$data->scheduledtime',
            'headerHtmlOptions' => array('colgroup' => 'cols-1'),
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

    $this->widget('application.widgets.ColGroupGridView', array(
        'itemsCssClass' => 'standard clickable-rows',
        'dataProvider' => $worklist_patients,
        'htmlOptions' => array('id' => "worklist-table-{$worklist->id}", 'class' => ''),
        'summaryText' => '<h3><small> {start}-{end} of {count} </small></h3>',
        'template' => '{items}',
        'columns' => $cols,
        'enableHistory' => true,
        'enablePagination' => false,
        'rowCssClass' => array('worklist-row'),
    ));
} ?>
</div>

<script>
    $(document).ready(function () {
        $(".worklist-row").click(function () {
            window.document.location = $(this).find('.js-worklist-url').data('url');
        })
    })
</script>
