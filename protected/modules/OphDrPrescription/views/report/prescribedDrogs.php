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
<div class="box reports">
    <div class="report-fields lettersReport">
        <h2>Prescribed drugs report</h2>
        <?php
        $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
                'id'=>'report-form',
                'enableAjaxValidation'=>false,
                'layoutColumns'=>array('label'=>2,'field'=>10),
                'action' => Yii::app()->createUrl('/OphDrPrescription/report/downloadReport'),
        ))?>

        <input type="hidden" name="report-name" value="PrescribedDrugs" />
        <div class="row field-row">
            <div class="large-2 column">
                <label for="phrases">
                        Drugs:
                </label>
            </div>
            <div class="large-5 column end phraseList">
                <div>
                    <?php
                    $defaultURL = "/" . Yii::app()->getModule('OphDrPrescription')->id . "/" . Yii::app()->getModule('OphDrPrescription')->defaultController;

                    $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
                            'name' => 'drug_id',
                            'id' => 'autocomplete_drug_id',
                            'source' => "js:function(request, response) {
                                                            $.getJSON('" . $defaultURL . "/DrugList', {
                                                                    term : request.term,
                                                                    type_id: $('#drug_type_id').val(),
                                                                    preservative_free: ($('#preservative_free').is(':checked') ? '1' : ''),
                                                            }, response);
                                                    }",
                            'options' => array(
                                    'select' => "js:function(event, ui) {
                                                        addItem(ui.item);
                                                        $(this).val('');
                                                        return false;
                                                }",
                            ),
                            'htmlOptions' => array(
                                    'placeholder' => 'search for drugs',
                            )
                    )); ?>
                </div>
            </div>
        </div>

        <div class="row field-row">
            <div class="large-2 column">
                    <label></label>
            </div>
            <div class="large-7 column end">
                <table id="report-drug-list">
                </table>
            </div>
        </div>

        <div class="row field-row">
            <div class="large-2 column">
                <label for="start_date">
                        Date from:
                </label>
            </div>
            <div class="large-2 column end">
                <?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                        'name' => 'start_date',
                        'options' => array(
                                'showAnim' => 'fold',
                                'dateFormat' => Helper::NHS_DATE_FORMAT_JS
                        ),
                        'value' => date('j M Y',strtotime('-1 year')),
                ))?>
            </div>
        </div>

        <div class="row field-row">
            <div class="large-2 column">
                <label for="end_date">
                        Date to:
                </label>
            </div>
            <div class="large-2 column end">
                <?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                    'name' => 'end_date',
                    'options' => array(
                            'showAnim' => 'fold',
                            'dateFormat' => Helper::NHS_DATE_FORMAT_JS
                    ),
                    'value' => date('j M Y'),
                ))?>
            </div>
        </div>  

        <?php $this->endWidget()?>
    </div>

    <div class="errors alert-box alert with-icon" style="display: none">
        <p>Please fix the following input errors:</p>
        <ul>
        </ul>
    </div>

    <div style="margin-top: 2em;">
            <button type="submit" class="classy blue mini display-report" name="run"><span class="button-span button-span-blue">Display report</span></button>
            <button type="submit" class="classy blue mini download-report" name="run"><span class="button-span button-span-blue">Download report</span></button>
            <img class="loader" style="display: none;" src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif')?>" alt="loading..." />&nbsp;
    </div>

    <div class="reportSummary report curvybox white blueborder" style="display: none;">
    </div>
</div>
