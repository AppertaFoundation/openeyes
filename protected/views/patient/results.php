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
/**
 * @var $search_terms array
 */

$based_on = array();
$search_term = "";
if ($search_terms['last_name']) {
    $based_on[] = 'LAST NAME';
    $search_term = $search_terms['last_name'];
}
if ($search_terms['first_name']) {
    $based_on[] = 'FIRST NAME';
    $search_term = $search_terms['first_name'];
}
if (isset($search_terms['dob']) && $search_terms['dob']) {
    $based_on[] = 'DOB';
    $search_term .= ' ' . $search_terms['dob'];
}
if ($search_terms['patient_identifier_value']) {
    $based_on[] = 'PATIENT IDENTIFIER';
    $search_term = is_array($search_terms['patient_identifier_value']) ? implode(',', $search_terms['patient_identifier_value']) : $search_terms['patient_identifier_value'];
}
$core_api = new CoreAPI();

$based_on = implode(', ', $based_on);

$institution = Institution::model()->getCurrent();
$selected_site_id = Yii::app()->session['selected_site_id'];
$primary_identifier_usage_type = SettingMetadata::model()->getSetting('display_primary_number_usage_code');
$primary_identifier_prompt = PatientIdentifierHelper::getIdentifierDefaultPromptForInstitution(
    $primary_identifier_usage_type,
    $institution->id,
    $selected_site_id
);

?>
<div class="oe-full-header flex-layout">
    <div class="title wordcaps">Patient search</div>
</div>

<div class="oe-full-content subgrid oe-patient-search">
  <nav class="oe-full-side-panel">
    <h3>Results</h3>
      <table class="standard">
          <tbody>
          <tr>
              <th>Search</th>
              <td>"<?php echo $search_term ?>"</td>
          </tr>
          <tr>
              <th>Found</th>
              <td><?php echo $total_items ?> patients</td>
          </tr>
          <tr>
              <th>Based on</th>
              <td><?php echo $based_on ?></td>
          </tr>

          </tbody>
      </table>
      <hr class="divider">
      <h3>Search</h3>
      <?php $this->renderPartial('//base/_search_bar', array(
          'callback' => Yii::app()->createUrl('site/search'),
          'context' => 'sidebar',
      )); ?>
      <?php if ($this->checkAccess('Advanced Search')) { ?>
          <hr class="divider">
          <h4>Use the <a href="<?= Yii::app()->createUrl('/OECaseSearch/caseSearch/index'); ?>" class="hint">advanced search</a></h4>
      <?php } ?>
  </nav>
  <div class="results-all">
        <?php $this->renderPartial('//base/_messages');
        $dataProvided = $data_provider->getData();
        $items_per_page = $data_provider->getPagination()->getPageSize();
        $page_num = $data_provider->getPagination()->getCurrentPage();
        $from = ($page_num * $items_per_page) + 1;
        $to = ($page_num + 1) * $items_per_page;
        if ($to > $total_items) {
            $to = $total_items;
        }
        ?>
    <table class="standard search-results clickable-rows">
        <colgroup>
        </colgroup>
      <thead>
      <tr>
            <?php foreach (
            array(
                             $primary_identifier_prompt,
                             'Title',
                             'First name',
                             'Last name',
                             'Born',
                             'Age',
                             'Sex'
                         ) as $i => $field
) { ?>
              <th id="patient-grid_c<?= $i ?>">
                  <?php
                    $new_sort_dir = 0;
                    if ($i > 0 && $i == $sort_by) {
                        $new_sort_dir = 1 - $sort_dir;
                        echo CHtml::link(
                            $field,
                            Yii::app()->createUrl(
                                'patient/search',
                                array('term' => $term, 'sort_by' => $i, 'sort_dir' => $new_sort_dir, 'page_num' => $page_num)
                            ),
                            array('class' => in_array($i, array(0, 2, 4, 5)) ? (($sort_dir == 0) ? 'sortable column-sort ascend ' : 'sortable column-sort descend ' . 'active') : '')
                        );
                        ?>
                    <?php } elseif ($i > 0) {
                        echo CHtml::link(
                            $field,
                            Yii::app()->createUrl(
                                'patient/search',
                                array('term' => $term, 'sort_by' => $i, 'sort_dir' => $new_sort_dir, 'page_num' => $page_num)
                            ),
                            array('class' => in_array($i, array(0, 2, 4, 5)) ? 'sortable' : '')
                        );
                    } else {
                        echo $field;
                    }
                    ?>
              </th>
            <?php } ?>
      </tr>
      </thead>
      <tbody>
        <?php
            $institution = Institution::model()->getCurrent();
            $site_id = Yii::app()->session['selected_site_id'];
        ?>
        <?php foreach ($dataProvided as $i => $patient) { ?>
            <?php
                $primary_identifier = PatientIdentifierHelper::getIdentifierForPatient(
                    \SettingMetadata::model()->getSetting('display_primary_number_usage_code'),
                    $patient->id,
                    $institution->id,
                    $site_id
                );

            if (!$primary_identifier) {
                $primary_identifier = PatientIdentifierHelper::getIdentifierForPatient(
                    \SettingMetadata::model()->getSetting('display_secondary_number_usage_code'),
                    $patient->id,
                    $institution->id,
                    $site_id
                );
            }
            ?>

        <tr id="r<?php echo $patient->id ?>" class="clickable found-patient"
            data-link="<?php echo $core_api->generatePatientLandingPageLink($patient); ?>"
            <?php
            // data-patient_identifier_value used when the object is unsaved - means it came from PAS
            // PAS responsible for setting up this relation
            // for already saved patient we don't care this value here, patient.id will be used to navigate the user
            // to the patient summary page
            echo "data-patient_identifier_value='" . ($patient->localIdentifiers[0]->value ?? $patient->globalIdentifiers[0]->value ?? '') . "'";
            if ($patient->isNewRecord) {
                echo " data-is_new_record='1'";
                echo " data-patient_identifier_type_id=" . $patient->localIdentifiers[0]->patientIdentifierType->id ?? $patient->globalIdentifiers[0]->patientIdentifierType->id ?? '';
            }
            ?>
        >
            <td><?= $primary_identifier->value ?? $patient->localIdentifiers[0]->value ?? ''; ?>
                <?php $this->widget(
                    'application.widgets.PatientIdentifiers',
                    [
                        'patient' => $patient,
                        'show_all' => true
                    ]
                ); ?>
                <?= $patient->isNewRecord ? '| From PAS' : '| From Local DB'?>
            </td>
                  <td><?php echo $patient->title ?></td>
                  <td><?php echo $patient->first_name ?></td>
                  <td><?php echo $patient->last_name ?></td>
                  <td><?php echo $patient->dob ? (date('d/m/Y', strtotime($patient->dob))) : ''; ?></td>
                  <td><?php echo $patient->getAge(); ?></td>
                  <td><?php echo $patient->gender ?></td>
                </tr>
        <?php } ?>
      </tbody>
      <tfoot>
      <tr>
        <td colspan="8">
            <?php $this->widget('LinkPager', [ 'pages' => $data_provider->getPagination() ]); ?>
        </td>
      </tr>
      </tfoot>
    </table>
  </div>
</div>

<script>
    document.addEventListener('click', function(e) {
        // loop parent nodes from the target to the delegation node
        for (var target = e.target; target && target != this; target = target.parentNode) {
            if (target.matches('tr.clickable')) {
                patientSearch.call(target, e); // target will be 'this' in patientSearch() fn
                break;
            }
        }
    }, false);


    function patientSearch() {
        let url;

        if (this.getAttribute('data-is_new_record') === '1' && this.getAttribute('data-patient_identifier_value') !== undefined) {
            url = '<?php echo Yii::app()->createUrl('patient/search')?>?term=' + this.getAttribute('data-patient_identifier_value')
            + '&patient_identifier_type_id=' + this.getAttribute('data-patient_identifier_type_id')
        } else {
            url = this.getAttribute('data-link');
        }
        window.location.href = url;
        return false;
    }

    $('#js-clear-search-btn').click(function () {
        window.location.assign('<?php echo Yii::app()->createURL('site/index') ; ?>');
    });
</script>
