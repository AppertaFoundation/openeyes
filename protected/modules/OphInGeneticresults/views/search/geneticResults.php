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
<div class="box admin">
  <h2>Genetic test/result</h2>


        <?php
        $institution = Institution::model()->getCurrent();
        $selected_site_id = Yii::app()->session['selected_site_id'];
        $primary_identifier_usage_type = Yii::app()->params['display_primary_number_usage_code'];

        $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
          'id' => 'searchform',
          'enableAjaxValidation' => false,
          'focus' => '#search',
          'action' => Yii::app()->createUrl('/OphInGeneticresults/search/geneticResults'),
        )) ?>
    <div class="cols-12 column">
      <div class="cols-12 column">
        <table class="standard">
              <thead>
              <tr>
                <th>Subject Id</th>
                <th>Family Id</th>
                <th>Gene</th>
                <th>Method</th>
                <th>Homozygosity</th>
                <th>Effect</th>
              </tr>
              </thead>
              <tbody>
              <tr>

                <td>
                    <?=\CHtml::textField('genetics-patient-id', @$_GET['genetics-patient-id']) ?>
                </td>
                 <td>
                    <?=\CHtml::textField('genetics-pedigree-id', @$_GET['genetics-pedigree-id']) ?>
                </td>

                <td>
                    <?=\CHtml::dropDownList(
                        'gene-id',
                        @$_GET['gene-id'],
                        CHtml::listData(PedigreeGene::model()->findAll(array('order' => 'name asc')), 'id', 'name'),
                        array('empty' => '- All -')
                    ) ?>
                </td>
                <td>
                    <?=\CHtml::dropDownList(
                        'method-id',
                        @$_GET['method-id'],
                        CHtml::listData(OphInGeneticresults_Test_Method::model()->findAll(array('order' => 'name asc')), 'id', 'name'),
                        array('empty' => '- All -')
                    ) ?>
                </td>
                <td>
                    <?=\CHtml::dropDownList('homo', @$_GET['homo'], array(1 => 'Yes', 0 => 'No'), array('empty' => '- All -')) ?>
                </td>
                <td>
                    <?=\CHtml::dropDownList(
                        'effect-id',
                        @$_GET['effect-id'],
                        CHtml::listData(OphInGeneticresults_Test_Effect::model()->findAll(array('order' => 'name asc')), 'id', 'name'),
                        array('empty' => '- All -')
                    ) ?>
                </td>
              </tr>
              </tbody>
            </table>
        <table class="standard">
              <thead>
              <tr>
                <th>Result date from</th>
                <th>Result date to</th>
                <th>Text search</th>
                <th>Diagnosis search</th>
              </tr>
              </thead>
              <tbody>
              <tr>
                <td>
                    <?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                        'name' => 'date-from',
                        'id' => 'date-from',
                        'options' => array(
                            'showAnim' => 'fold',
                            'dateFormat' => Helper::NHS_DATE_FORMAT_JS,
                        ),
                        'value' => @$_GET['date-from'],
                    )) ?>
                </td>
                  <td>
                    <?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                        'name' => 'date-to',
                        'id' => 'date-to',
                        'options' => array(
                            'showAnim' => 'fold',
                            'dateFormat' => Helper::NHS_DATE_FORMAT_JS,
                        ),
                        'value' => @$_GET['date-to'],
                    )) ?>
                </td>
                <td>
                    <?=\CHtml::textField('query', @$_GET['query']) ?>
                </td>
                <td>
                    <div id="diagnosis-search">
                        <?php
                            $value = Yii::app()->request->getQuery('genetics-patient-disorder-id');
                        ?>
                        <!-- <label for="GeneticsPatient_comments">Search for a diagnosis</label> -->
                        <span id="enteredDiagnosisText" class="<?php echo $value ? '' : 'hidden' ?>" style="display:block; margin-bottom:5px;">
                            <?php
                            if ($value) {
                                $disorder = Disorder::model()->findByPk($value);
                                echo $disorder->term;
                                ?><i class="oe-i remove-circle small" aria-hidden="true" id="clear-diagnosis-widget"></i><?php
                            }
                            ?>
                        </span>
                        <?php
                        $this->renderPartial('//disorder/disorderAutoComplete', array(
                            'class' => 'search',
                            'name' => 'genetics-patient-disorder-id',
                            'code' => '',
                            'value' => $value,
                            'clear_diagnosis' => '&nbsp;<i class="oe-i remove-circle small" aria-hidden="true" id="clear-diagnosis-widget"></i>',
                            'placeholder' => 'Search for a diagnosis',
                        ));
                        ?>
                    </div>
                </td>
              </tr>
              </tbody>
            </table>
        <button id="search_tests" class="secondary right" type="submit">
          Search
        </button>
      </div>
    </div>
        <?php $this->endWidget() ?>
    <div style="clear:both"></div>
    <hr>

  <h2>Genetic test events</h2>

  <form id="genetics_result">
    <input type="hidden" id="select_all" value="0"/>

        <?php if (count($genetic_tests) < 1) { ?>
        <div class="alert-box no_results">
          <span class="column_no_results">
                  <?php if (!empty($_GET)) { ?>
                    No results found.
                    <?php } else { ?>
                    Enter criteria to search for genetic results.
                    <?php } ?>
          </span>
        </div>
        <?php } ?>

        <?php if (!empty($genetic_tests)) { ?>
        <table class="standard">
          <thead>
          <tr>
            <th><?=\CHtml::link('Result date', $this->getUri(array('sortby' => 'date'))) ?></th>
<!--            <th>--><?php //echo CHtml::link('Subject Id', $this->getUri(array('sortby' => 'genetics-patient-id'))) ?><!--</th>-->
            <th><?=\CHtml::link('Hospital no', $this->getUri(array('sortby' => 'hos_num'))) ?></th>
            <th><?=\CHtml::link('Family Id', $this->getUri(array('sortby' => 'genetics-pedigree-id'))) ?></th>
            <th><?=\CHtml::link('Patient name', $this->getUri(array('sortby' => 'patient_name'))) ?></th>
            <th>Maiden Name</th>
            <th><?=\CHtml::link('Gene', $this->getUri(array('sortby' => 'gene'))) ?></th>
<!--            <th>--><?php //echo CHtml::link('Method', $this->getUri(array('sortby' => 'method'))) ?><!--</th>-->
            <th><?=\CHtml::link('Hom', $this->getUri(array('sortby' => 'homo'))) ?></th>
        <th><?=\CHtml::link('Base Change', $this->getUri(array('sortby' => 'base_change'))) ?></th>
        <th><?=\CHtml::link('Amino Acid Change', $this->getUri(array('sortby' => 'amino_acid_change'))) ?></th>
<!--            <th>--><?php //echo CHtml::link('Result', $this->getUri(array('sortby' => 'result'))) ?><!--</th>-->
            <th><?=\CHtml::link('Effect', $this->getUri(array('sortby' => 'effect'))) ?></th>
          </tr>
          </thead>
          <tbody>
            <?php foreach ($genetic_tests as $i => $test) { ?>
            <tr class="clickable" data-uri="<?php echo Yii::app()->createUrl('/OphInGeneticresults/default/view/' . $test->event_id) ?>">
                  <td><?php echo $test->NHSDate('result_date') ?></td>
<!--                  <td>--><?php //echo CHtml::link($test->event->episode->patient->geneticsPatient->id, '/Genetics/subject/view/id/' . $test->event->episode->patient->geneticsPatient->id ); ?><!--</td>-->
                  <td>
                      <?php $primary_identifier = PatientIdentifierHelper::getIdentifierForPatient(Yii::app()->params['display_primary_number_usage_code'],
                          $test->event->episode->patient->id, $institution->id, $selected_site_id); ?>
                      <?= PatientIdentifierHelper::getIdentifierValue($primary_identifier) ?>
                      <?php $this->widget(
                          'application.widgets.PatientIdentifiers',
                          [
                              'patient' => $test->event->episode->patient,
                              'show_all' => true,
                              'tooltip_size' => 'small'
                          ]); ?>
                  </td>
                <td>
                    <?php foreach ($test->event->episode->patient->geneticsPatient->pedigrees as $i => $pedigrees) : ?>
                        <?php if ($i > 0) {
                            echo ", ";
                        } ?>
                        <?=\CHtml::link($pedigrees->id, '/Genetics/pedigree/view/id/' . $pedigrees->id); ?>
                    <?php endforeach; ?>
                </td>
                  <td><?php echo strtoupper($test->event->episode->patient->last_name) ?>, <?php echo $test->event->episode->patient->first_name ?></td>
                  <td><?php echo $test->event->episode->patient->contact->maiden_name ?></td>
                  <td><?php echo str_replace(',', ', ', $test->gene->name) ?></td>
<!--                  <td>--><?php //echo $test->method->name ?><!--</td>-->
                  <td><?php echo $test->homo ? 'Yes' : 'No' ?></td>
                  <td><?php echo $test->base_change ?></td>
                  <td><?php echo $test->amino_acid_change ?></td>
<!--                <td>--><?php //echo $test->result ?><!--</td>-->
                <td><?php echo $test->effect->name ?></td>
            </tr>
            <?php } ?>
          </tbody>
          <tfoot class="pagination-container">
          <tr>
              <td colspan="3">
                  <?php $to = ($pagination->getItemCount() < $pagination->limit) ? $pagination->getItemCount() : ($pagination->limit * ($pagination->getCurrentPage()+1)); ?>
                  Showing <?php echo $pagination->offset + 1; ?> to <?php echo $to; ?> of <?php echo $pagination->getItemCount(); ?>
              </td>
            <td colspan="6">
                <?php
                    $this->widget('CLinkPager', array(
                        'currentPage' => $pagination->getCurrentPage(),
                        'itemCount' => $pagination->getItemCount(),
                        'pageSize' => $pagination->getPageSize(),
                        'maxButtonCount' => 10,
                        'header'=> '',
                        'htmlOptions'=>array('class'=>'pagination right'),
                        'selectedPageCssClass' => 'current'
                    ));
                ?>
            </td>
          </tr>
          </tfoot>
        </table>
        <?php } ?>
  </form>
</div>
