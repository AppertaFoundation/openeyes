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
<div class="box reports">
    <div class="report-fields lettersReport">
        <h2>Prescribed drugs report</h2>
        <?php
        $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
                'id' => 'report-form',
                'enableAjaxValidation' => false,
                'layoutColumns' => array('label' => 2, 'field' => 10),
                'action' => Yii::app()->createUrl('/OphDrPrescription/report/downloadReport'),
        ))?>

        <input type="hidden" name="report-name" value="PrescribedDrugs" />
        
        <div class="row field-row">
            
            <div class="large-6 column">
        
                <div class="row field-row">

                    <div class="large-2 column">
                        <label for="phrases">Drugs:</label>
                    </div>
                    <div class="large-10 column">
                        <?php
                            // set name to null as it is not required to send this value to the server
                            echo CHtml::dropDownList(null, null,
                                CHtml::listData($drugs, 'id', 'tallmanlabel'), array('empty' => '-- Select --', 'id' => 'drug_id'));
                        ?>
                    </div>
                </div>
                
                <div class="row field-row">
                    <div class="large-2 column">
                        <label for="phrases">
                        </label>
                    </div>
                    <div class="large-9 column end phraseList">
                        <div>
                            <?php
                            $defaultURL = '/'.Yii::app()->getModule('OphDrPrescription')->id.'/'.Yii::app()->getModule('OphDrPrescription')->defaultController;

                            $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
                                    'name' => 'drug_id',
                                    'id' => 'autocomplete_drug_id',
                                    'source' => "js:function(request, response) {

                                                        $.ajax({
                                                            dataType: 'json',
                                                            url: '".$defaultURL."/DrugList',
                                                            data: {
                                                                term : request.term,
                                                                type_id: $('#drug_type_id').val(),
                                                                preservative_free: ($('#preservative_free').is(':checked') ? '1' : ''),
                                                        },
                                                        success: function(result){ response(result); $('.autocomplete-loader').hide();},
                                                        beforeSend: function(){ $('.autocomplete-loader').show(); }
                                                      });
                                                            }",
                                    'options' => array(
                                            'select' => "js:function(event, ui) {
                                                                var tr = $('#report-drug-list').find('tr#' + ui.item.id);
                                                                if( tr.length == 0 ){
                                                                    $('.no-drugs').hide();
                                                                    addItem(ui.item);
                                                                }
                                                                $(this).val('');
                                                                return false;
                                                        }",
                                    ),
                                    'htmlOptions' => array(
                                            'placeholder' => 'search for drugs',
                                    ),
                            )); ?>
                        </div>
                    </div>
                    <div class="large-1 column end">
                        <img class="autocomplete-loader" style="display: none;" src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif')?>" alt="loading...">
                    </div>
                </div>

                <div class="row field-row">
                    <div class="large-12 column"></div>
                </div>
                <div class="row field-row">
                    <div class="large-2 column" style="padding-right:0px;">
                        <label for="start_date">
                                Date from:
                        </label>
                    </div>
                    <div class="large-3 column end">
                        <?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                                'name' => 'OphDrPrescription_ReportPrescribedDrugs[start_date]',
                                'options' => array(
                                        'showAnim' => 'fold',
                                        'dateFormat' => Helper::NHS_DATE_FORMAT_JS,
                                ),
                                'value' => date('j M Y', strtotime('-1 year')),
                        ))?>
                    </div>
                </div>

                <div class="row field-row">
                    <div class="large-2 column">
                        <label for="end_date">
                                Date to:
                        </label>
                    </div>
                    <div class="large-3 column end">
                        <?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                            'name' => 'OphDrPrescription_ReportPrescribedDrugs[end_date]',
                            'options' => array(
                                    'showAnim' => 'fold',
                                    'dateFormat' => Helper::NHS_DATE_FORMAT_JS,
                            ),
                            'value' => date('j M Y'),
                        ))?>
                    </div>
                </div>
                <div class="row field-row">
                    <div class="large-12 column"></div>
                </div>
                <div class="row field-row">
                        <div class="large-2 column">
                            <label for="author_id">User</label>
                        </div>
                        <div class="large-7 column end">
                            <?php if ( Yii::app()->getAuthManager()->checkAccess('Report', Yii::app()->user->id) ):?>
                                <?php echo CHtml::dropDownList('OphDrPrescription_ReportPrescribedDrugs[user_id]', '',
                                                                CHtml::listData($users, 'id', 'fullName'), array('empty' => '--- Please select ---'))?>
                            <?php else: ?>
                                <?php
                                    $user = User::model()->findByPk(Yii::app()->user->id);
                                    echo CHtml::dropDownList(null, '',
                                        array(Yii::app()->user->id => $user->fullName),
                                        array('disabled' => 'disabled', 'readonly' => 'readonly', 'style'=>'background-color:#D3D3D3;') //for some reason the chrome doesn't gray out
                                    );
                                    echo CHtml::hiddenField('OphDrPrescription_ReportPrescribedDrugs[user_id]', Yii::app()->user->id);
                                ?>
                            <?php endif ?>
                        </div>
                </div>
                
                <div class="row field-row">
                        <div class="large-12 column">
                             <div style="margin-top: 2em;">
                                    <button type="submit" class="classy blue mini display-report" name="run"><span class="button-span button-span-blue">Display report</span></button>
                                    <button type="submit" class="classy blue mini download-report" name="run"><span class="button-span button-span-blue">Download report</span></button>
                                    <img class="loader" style="display: none;" src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif')?>" alt="loading..." />&nbsp;
                            </div>
                        </div>
                        
                </div>
                
            </div>
            
            <div class="large-6 column">
                <div style="" class="panel procedures">
                    <table class="plain" id="report-drug-list">
                        <thead>
                            <tr>
                                <th>Drug name</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody class="body">
                            <tr class="no-drugs">
                                <td>No drugs selected</td>
                                <td class="right"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <?php $this->endWidget()?>
    </div>

    <div class="errors alert-box alert with-icon" style="display: none">
        <p>Please fix the following input errors:</p>
        <ul>
        </ul>
    </div>

   

    <div class="reportSummary report curvybox white blueborder" style="display: none;">
    </div>
</div>
