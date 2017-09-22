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
	<div class="report-fields">
		<h2>Diagnoses report</h2>
		<?php
        $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
            'id' => 'report-form',
            'enableAjaxValidation' => false,
            'layoutColumns' => array('label' => 2, 'field' => 10),
            'action' => Yii::app()->createUrl('/report/downloadReport'),
        ))?>

		<input type="hidden" name="report-name" value="Diagnoses" />

		<div class="row field-row">
			<div class="large-2 column">
				<label for="start_date">
					Start date:
				</label>
			</div>
			<div class="large-2 column end">
				<?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                    'name' => 'start_date',
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
					End date:
				</label>
			</div>
			<div class="large-2 column end">
				<?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                    'name' => 'end_date',
                    'options' => array(
                        'showAnim' => 'fold',
                        'dateFormat' => Helper::NHS_DATE_FORMAT_JS,
                    ),
                    'value' => date('j M Y'),
                ))?>
			</div>
		</div>

		<div class="row field-row">
			<div class="large-12 column end">
				<div class="whiteBox forClinicians">
					<div class="data_row">
						<table class="subtleWhite">
							<thead>
								<tr>
									<th style="width: 400px;">Diagnosis</th>
									<th>Principal</th>
									<th>Edit</th>
								</tr>
							</thead>
							<tbody id="Reports_diagnoses">
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>

		<div id="selected_diagnoses">
		</div>

		<?php $this->widget('application.widgets.DiagnosisSelection', array(
                'field' => 'disorder_id',
                'options' => CommonOphthalmicDisorder::getList(Firm::model()->findByPk($this->selectedFirmId)),
                'layout' => 'minimal',
                'callback' => 'Reports_AddDiagnosis',
        ))?>

		<div class="row field-row">
			<div class="large-2 column">
			</div>
			<div class="large-9 column end">
				<input type="radio" name="condition_type" id="condition_or" value="or" checked="checked" />
				<label for="condition_or">
					Match patients with <strong>any</strong> of these diagnoses
				</label>
			</div>
		</div>
		<div class="row field-row">
			<div class="large-2 column">
			</div>
			<div class="large-9 column end">
				<input type="radio" name="condition_type" id="condition_and" value="and" />
				<label for="condition_and">
					Match patients with <strong>all</strong> of these diagnoses
				</label>
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
