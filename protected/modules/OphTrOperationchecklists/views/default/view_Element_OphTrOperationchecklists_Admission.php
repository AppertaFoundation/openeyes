<?php
/**
 * (C) Copyright Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * @var Element_OphTrOperationchecklists_Admission $element
 * @var bool $isCollapsable
 */
?>
<?php
$api = Yii::app()->moduleAPI->get('OphInLabResults');
$covid19Result = $api->getLabResultTypeResult($this->patient->id, $this->event->id, "COVID-19");

$checklistResults = $element->checklistResults;
$this->renderPartial('checklist_view', array(
    'element' => $element,
    'checklistResults' => $checklistResults,
    'isCollapsable' => $isCollapsable ?? null,
    'resultModel' => OphTrOperationchecklists_AdmissionResults::model()
    ));
?>

<script>
    $(document).ready(function () {
        // COVID-19 Question
        let covid19LabResult = <?= json_encode($covid19Result); ?>;
        if (covid19LabResult) {
            const commentHtml = `<div class="data-group"><br>
                                <div class="cols-4 column large-push-1" style="font-style: italic;">Comments</div>
                                <div class="cols-6 column large-push-1 end">${covid19LabResult.comment}</div>
                                </div>`;
            $("td:contains(COVID-19)").next().html(covid19LabResult.result);
            $("td:contains(COVID-19)").closest("td").append(commentHtml);
        } else {
            const htmlString = `Not recorded
                                <i class="js-has-tooltip oe-i info small pad right" data-tooltip-content="COVID-19
                                result is not recorded for this patient. This can be recorded in the Lab Results
                                event."></i>`;
            $("td:contains(COVID-19)").next().html(htmlString);
        }
    });
</script>