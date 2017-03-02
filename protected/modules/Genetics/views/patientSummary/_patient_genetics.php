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
      <?php if ($subject && $subject->pedigrees) { ?>
            <div class="row data-row">
                <div class="large-4 column">
                    <div class="data-label">Genetic database ID:</div>
                </div>
                <div class="large-8 column">
                     <div class="data-value">
                        <?php echo $subject->id; ?>
                        ( <?php echo CHtml::link('View', Yii::app()->createUrl('/Genetics/subject/view/' . $subject->id)) ?> )
                     </div>
                </div>
            </div>
          <?php foreach($subject->pedigrees as $pedigree) {  ?>
            <div class="row data-row">
                <div class="large-4 column">
                    <div class="data-label">Pedigree ID:</div>
                 </div>
                <div class="large-8 column">
                    <div class="data-value">
                        <?php echo $pedigree->id ?>
                        ( <?php echo CHtml::link('View', Yii::app()->createUrl('/Genetics/pedigree/view/' . $pedigree->id)) ?> )
                    </div>
                </div>
            </div>
          <?php } ?>
           
            <div class="row data-row">
                <div class="large-4 column">
                    <div class="data-label">Diagnosis:</div>
                 </div>
                <div class="large-8 column">
                    <div class="data-value">
                        <?php
                            
                            if($subject->diagnoses !== NULL){
                                foreach($subject->diagnoses as $diagnose){
                                    echo $diagnose->fully_specified_name."<br>";
                                }
                            } 
                        ?>
                    </div>
                </div>
            </div>
            <div class="row data-row">
                <div class="large-4 column">
                    <div class="data-label">Status:</div>
                 </div>
                <div class="large-8 column">
                    <div class="data-value">
                        <?php 
                        
                        foreach($subject->pedigrees as $pedigree) {
                            $status = GeneticsPatientPedigree::model()->findByAttributes(array('patient_id' => $subject->id, 'pedigree_id' => $pedigree->id));
                            if($status->status !== NULL){
                                echo $status->status->name;
                            }
                        }
                        ?>

                    </div>
                </div>
            </div>
            <div class="row data-row">
                <div class="large-4 column">
                    <div class="data-label"><?php echo $subject->getAttributeLabel('comments') ?>:</div>
                </div>
                <div class="large-8 column">
                     <div class="data-value"><?php echo Yii::app()->format->ntext($pedigree->comments) ?></div>
                </div>
            </div>
      <?php } else { ?>
        <div class="row data-row">
            
            <div class="data-label column" style="margin-bottom:10px;">This patient has no recorded pedigree.</div>
            <div id="add_new_pedigree">
                <div class="large-3 column">
                    <label class="align">Set pedigree:</label>
                </div>
                <div class="large-5 column">   
                <?php
                    $model = Pedigree::model()->getAllIdAndText();
                    echo CHtml::dropDownList('pedigreeSelect','pedigreeSelect', CHtml::listData(
                        $model,
                        'id',
                        'text'
                    ));
                ?>
                    <input type="hidden" id="patient_id" name="patient_id" value="<?php echo $patient->id; ?>" />
                </div>
                <div class="large-4 column">
                    <button id="btn-save_pedigree" class="secondary small btn_save_pedigree">Save</button>
                </div>
                <div class="clearfix"></div>
            </div>
           
        </div>
      <?php } ?>
      <?php
      $api = Yii::app()->moduleAPI->get('OphInDnasample');
      if ($api) {
          $events = $api->getEventsByPatient($this->patient);
          if ($events) { ?>
            <div class="row data-row">
              <div class="large-4 column">
                <div class="data-label">DNA Sample Events:</div>
              </div>
              <div class="large-8 column">
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
          $events = $api->getEventsByPatient($this->patient);
          if ($events) { ?>
            <div class="row data-row">
              <div class="large-4 column">
                <div class="data-label">DNA Extraction Events:</div>
              </div>
              <div class="large-8 column">
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
          $events = $api->getEventsByPatient($this->patient);
          if ($events) { ?>
            <div class="row data-row">
              <div class="large-4 column">
                <div class="data-label">Genetic Result Events:</div>
              </div>
              <div class="large-8 column">
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
<script type="text/javascript">
    $(document).ready(function(){
        
       
        $('#btn-save_pedigree').click(function(){
            $('#btn-save_pedigree').attr('disabled',true);
            $.ajax({
                'type': 'POST',
                'url': baseUrl+'/Genetics/default/savePedigree',
                'data': {
                    YII_CSRF_TOKEN:YII_CSRF_TOKEN, 
                    pedigree_id: $('#pedigreeSelect').val(),
                    patient_id: $('#patient_id').val()
                },
                'success': function(response) {
                    window.location.reload();
                },
                'error': function() {
                    new OpenEyes.UI.Dialog.Alert({
                        content: "Sorry, an internal error occurred.\n\nPlease contact support for assistance."
                    }).open();
                     $('#btn-save_pedigree').attr('disabled',false);
                }
            });
        });
    }); 
</script>
