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
<div class="box reports">
	<div class="report-fields lettersReport">
		<h2>Letters report</h2>
		<?php
		$form = $this->beginWidget('BaseEventTypeCActiveForm', array(
			'id'=>'report-form',
			'enableAjaxValidation'=>false,
			'layoutColumns'=>array('label'=>2,'field'=>10),
			'action' => Yii::app()->createUrl('/OphCoCorrespondence/report/downloadReport'),
		))?>

		<input type="hidden" name="report-name" value="Letters" />
		<div class="row field-row">
			<div class="large-2 column">
				<label for="phrases">
					Phrases:
				</label>
			</div>
			<div class="large-5 column end phraseList">
				<div>
					<?php echo CHtml::textField('OphCoCorrespondence_ReportLetters[phrases][]','')?>
				</div>
			</div>
		</div>

		<div class="row field-row">
			<div class="large-2 column">
				<label></label>
			</div>
			<div class="large-2 column end">
				<button type="submit" class="classy blue mini small" id="add_letter_phrase"><span class="button-span button-span-blue">Add</span></button>
			</div>
		</div>

		<div class="row field-row">
			<div class="large-2 column">
				<label>
					Search method:
				</label>
			</div>
			<div class="large-9 column end">
				<input type="radio" name="OphCoCorrespondence_ReportLetters[condition_type]" id="condition_or" value="or" checked="checked" />
				<label for="condition_or">
					Must contain <strong>any</strong> of the phrases
				</label>
			</div>
		</div>

		<div class="row field-row">
			<div class="large-2 column">
				<label></label>
			</div>
			<div class="large-9 column end">
				<input type="radio" name="OphCoCorrespondence_ReportLetters[condition_type]" id="condition_and" value="and" />
				<label for="condition_and">
					Must contain <strong>all</strong> of the phrases
				</label>
			</div>
		</div>

		<div class="row field-row">
			<div class="large-2 column">
				<label></label>
			</div>
			<div class="large-9 column end">
				<input type="hidden" name="OphCoCorrespondence_ReportLetters[match_correspondence]" value="0" />
				<input type="checkbox" id="match_correspondence" name="OphCoCorrespondence_ReportLetters[match_correspondence]" value="1" checked="checked" />
				<label for="match_correspondence">
					Match correspondence
				</label>
			</div>
		</div>

		<div class="row field-row">
			<div class="large-2 column">
				<label></label>
			</div>
			<div class="large-9 column end">
				<input type="hidden" name="OphCoCorrespondence_ReportLetters[match_legacy_letters]" value="0" />
				<input type="checkbox" id="match_legacy_letters" name="OphCoCorrespondence_ReportLetters[match_legacy_letters]" value="1" checked="checked" />
				<label for="match_legacy_letters">
					Match legacy letters
				</label>
			</div>
		</div>

		<div class="row field-row">
			<div class="large-2 column">
				<label for="OphCoCorrespondence_ReportLetters_start_date">
					Date from:
				</label>
			</div>
			<div class="large-2 column end">
				<?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
					'name' => 'OphCoCorrespondence_ReportLetters[start_date]',
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
				<label for="OphCoCorrespondence_ReportLetters_end_date">
					Date to:
				</label>
			</div>
			<div class="large-2 column end">
				<?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
					'name' => 'OphCoCorrespondence_ReportLetters[end_date]',
					'options' => array(
						'showAnim' => 'fold',
						'dateFormat' => Helper::NHS_DATE_FORMAT_JS
					),
					'value' => date('j M Y'),
				))?>
			</div>
		</div>

		<div class="row field-row">
			<div class="large-2 column">
				<label for="author_id">
					Author
				</label>
			</div>
			<div class="large-3 column end">
				<?php echo CHtml::dropDownList('OphCoCorrespondence_ReportLetters[author_id]','',CHtml::listData(User::model()->findAll(array('order' => 'first_name asc,last_name asc')),'id','fullName'),array('empty' => '--- Please select ---'))?>
			</div>
		</div>
		<div class="row field-row">
			<div class="large-2 column">
				<label for="site_id">
					Site
				</label>
			</div>
			<div class="large-3 column end">
				<?php echo CHtml::dropDownList('OphCoCorrespondence_ReportLetters[site_id]', '', Site::model()->getListForCurrentInstitution(), array('empty' => '--- Please select ---'))?>
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
<script type="text/javascript">
	$('input[name="OphCoCorrespondence_ReportLetters[phrases][]"]').focus();
</script>