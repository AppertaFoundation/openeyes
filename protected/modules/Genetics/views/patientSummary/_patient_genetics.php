<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<section class="box patient-info js-toggle-container">
  <h3 class="box-title">Genetics:</h3>
  <a href="#" class="toggle-trigger toggle-hide js-toggle">
    <span class="icon-showhide">
      Show/hide this section
    </span>
  </a>
  <div class="js-toggle-body">
    <?php $subject = GeneticsPatient::model()->findByAttributes(array('patient_id' => $patient->id));?>
      <?php if ($subject) { ?>
            <div class="data-group">
                <div class="cols-4 column">
                    <div class="data-label">Genetic database ID:</div>
                </div>
                <div class="cols-8 column">
                     <div class="data-value">
                        <?php echo $subject->id; ?>
                        ( <?=\CHtml::link('View', Yii::app()->createUrl('/Genetics/subject/view/' . $subject->id)) ?> )
                     </div>
                </div>
            </div>
          <?php if ($subject->pedigrees) {
            foreach($subject->pedigrees as $pedigree) {  ?>
            <div class="data-group">
                <div class="cols-4 column">
                    <div class="data-label">Pedigree ID:</div>
                 </div>
                <div class="cols-8 column">
                    <div class="data-value">

                        <?php echo $pedigree->id ?>
                        <?php if ($pedigree->gene) { echo '(Gene: '.$pedigree->gene->name.')'; } ?>
                        <?php
                        $status = GeneticsPatientPedigree::model()->findByAttributes(array('patient_id' => $subject->id, 'pedigree_id' => $pedigree->id));
                        if($status->status !== NULL){
                            echo $status->status->name;
                        }
                        ?>
                        ( <?=\CHtml::link('View', Yii::app()->createUrl('/Genetics/pedigree/view/' . $pedigree->id)); ?> )
                    </div>
                </div>
            </div>
          <?php }
            } ?>
           
            <div class="data-group">
                <div class="cols-4 column">
                    <div class="data-label">Diagnosis:</div>
                 </div>
                <div class="cols-8 column">
                    <div class="data-value">
                        <?php
                            
                            if($subject->diagnoses !== NULL){
                                foreach($subject->diagnoses as $diagnose){
                                    echo $diagnose->term."<br>";
                                }
                            } 
                        ?>
                    </div>
                </div>
            </div>
            <div class="data-group">
                <div class="cols-4 column">
                    <div class="data-label"><?php echo $subject->getAttributeLabel('comments') ?>:</div>
                </div>
                <div class="cols-8 column">
                     <div class="data-value"><?php echo Yii::app()->format->ntext($subject->comments) ?></div>
                </div>
            </div>
      <?php } else { ?>
        <div class="data-group">
            
            <div class="data-label column" style="margin-bottom:10px;">
                <?=\CHtml::link('Assign Patient to Genetics', Yii::app()->createUrl('Genetics/subject/edit?patient='.$patient->id)) ?>
            </div>
        </div>
      <?php } ?>
      <?php
      $api = Yii::app()->moduleAPI->get('OphInDnasample');
      if ($api) {
          $events = $api->getEventsByPatient($patient);
          if ($events) { ?>
            <div class="data-group">
              <div class="cols-4 column">
                <div class="data-label">DNA Sample Events:</div>
              </div>
              <div class="cols-8 column">
                <div class="data-value">
                    <?php foreach ($events as $event) {
                        echo EventNavigation::SmallIcon($event, '( View )');
                        echo ' ' . Helper::convertMySQL2NHS($event->event_date);
                        ?>
                      <br/>
                        <?php
                    }
                    ?>
                </div>
              </div>
            </div>
              <?php
          }
      }
      ?>
      <?php
      $api = Yii::app()->moduleAPI->get('OphInDnaextraction');
      if ($api) {
          $events = $api->getEventsByPatient($patient);
          if ($events) { ?>
            <div class="data-group">
              <div class="cols-4 column">
                <div class="data-label">DNA Extraction Events:</div>
              </div>
              <div class="cols-8 column">
                <div class="data-value">
                    <?php foreach ($events as $event) {
                        echo EventNavigation::SmallIcon($event, '( View )');
                        echo ' ' . Helper::convertMySQL2NHS($event->event_date);
                        ?>
                      <br/>
                        <?php
                    }
                    ?>
                </div>
              </div>
            </div>
              <?php
          }
      }
      ?>
      <?php
      $api = Yii::app()->moduleAPI->get('OphInGeneticresults');
      if ($api) {
          $events = $api->getEventsByPatient($patient);
          if ($events) { ?>
            <div class="data-group">
              <div class="cols-4 column">
                <div class="data-label">Genetic Result Events:</div>
              </div>
              <div class="cols-8 column">
                <div class="data-value">
                    <?php foreach ($events as $event) {
                        echo EventNavigation::SmallIcon($event,'( View )');
                        echo ' ' . Helper::convertMySQL2NHS($event->event_date);
                        ?>
                      <br/>
                        <?php
                    }
                    ?>
                </div>
              </div>
            </div>
              <?php
          }
      }
      ?>
  </div>
</section>
