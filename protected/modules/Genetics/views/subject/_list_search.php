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


    <?php $form=$this->beginWidget('CActiveForm', array(
        'action' => Yii::app()->createUrl($this->route),
        'method' => 'get',
        'id' => 'generic-search-form',
    )); ?>
    <div>

        <div>
            <?php echo $form->textField($model, 'id', ['placeholder' => 'Subject Id']); ?>
        </div>
        <div>
            <?php echo $form->textField($model, 'patient_pedigree_id', ['placeholder' => 'Family id']); ?>
        </div>

        <div>
            <?php
            $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                'name'  => 'patient-dob-id',
                'value' => Yii::app()->request->getQuery('patient-dob-id'),
                'id'    => 'GeneticsPatient_patient_dob',
                'options' => array(
                    'showAnim'      => 'fold',
                    'changeMonth'   => true,
                    'changeYear'    => true,
                    'altFormat'     => 'yy-mm-dd',
                    'altField'      => '#GeneticsPatient_patient_dob_alt',
                    'dateFormat'    => Helper::NHS_DATE_FORMAT_JS,
                    'yearRange'     => '-120:+0'
                ),
                'htmlOptions' =>array(
                    'autocomplete' => Yii::app()->params['html_autocomplete'],
                    'placeholder' => 'Date of Birth',
                )
            ));
            ?>
            <input type="hidden" name="GeneticsPatient[patient_dob]" id="GeneticsPatient_patient_dob_alt" value="<?=$model->patient_dob;?>"/>
        </div>

        <div>
            <?php echo $form->textField($model, 'patient_firstname', ['placeholder' => 'First name']); ?>
        </div>

        <div>
            <?php echo $form->textField($model, 'patient_lastname', ['placeholder' => 'Last name']); ?>
        </div>

        <div>
            <?php echo $form->textField($model, 'patient_maidenname', ['placeholder' => 'Maiden name']); ?>
        </div>

        <div>
            <?php echo $form->textField($model, 'comments', ['placeholder' => 'Comments']); ?>
        </div>
        <div>
            <?php echo $form->textField($model, 'patient_yob', ['placeholder' => 'Year of Birth']); ?>
        </div>
        <div id="diagnosis-search">
            <?php
                $query = Yii::app()->request->getQuery('search');
                $value = isset($query['patient_disorder_id']) ? $query['patient_disorder_id'] : '';
            ?>
            <!-- <label for="GeneticsPatient_comments">Search for a diagnosis</label> -->
            <span id="enteredDiagnosisText" class="<?php echo $value ? '' : 'hidden' ?>">
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
                    'name' => 'patient_disorder_id',
                    'code' => '',
                    'value' => $value,
                    'clear_diagnosis' => '&nbsp;<i class="oe-i remove-circle small" aria-hidden="true" id="clear-diagnosis-widget"></i>',
                    'placeholder' => 'Search for a diagnosis',
                    'callback' => null
                ));
                ?>
        </div>
        
        <div></div>


        <div class="submit-row text-right">
            <?=\CHtml::submitButton('Search', ['class' => 'button small primary event-action']); ?>
        </div>
    </div>
    <?php $this->endWidget(); ?>

