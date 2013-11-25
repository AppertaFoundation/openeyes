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
<div class="box admin">
	<h2>Patient search by diagnosis</h2>

	<div class="large-12 column">
		<?php
		$form = $this->beginWidget('BaseEventTypeCActiveForm',array(
			'id' => 'searchform',
			'enableAjaxValidation' => false,
			'focus' => '#search',
			'action' => Yii::app()->createUrl('/Genetics/search/geneticPatients'),
		))?>
			<div class="large-12 column">
				<div class="panel">
					<div class="row">
						<div class="large-9 column">
							<?php $form->widget('application.widgets.DiagnosisSelection',array(
								'value' => @$_GET['disorder-id'],
								'options' => CommonOphthalmicDisorder::getList(Firm::model()->findByPk($this->selectedFirmId)),
								'layoutColumns' => array(
									'label' => $form->layoutColumns['label'],
									'field' => 8,
								),
								'default' => false,
								'htmlOptions' => array(
									'hide_label' => true,
								),
								'allowClear' => true,
							))?>
						</div>
						<div class="large-3 column">
							<button id="search_patients" class="secondary" type="submit">
								Search
							</button>
						</div>
					</div>
				</div>
			</div>
		<?php $this->endWidget()?>
	</div>

	<h2>Genetics patients</h2>

	<form id="admin_sequences">
		<input type="hidden" id="select_all" value="0" />

		<?php if (count($patient_pedigrees) <1) {?>
			<div class="alert-box no_results">
				<span class="column_no_results">
					<?php if (@$_GET['gene-id'] || @$_GET['disorder-id']) {?>
						No genetics patients were found with the selected diagnosis.
					<?php }else{?>
						Please select a diagnosis to search for patients.
					<?php }?>
				</span>
			</div>
		<?php }?>

		<?php if (!empty($patient_pedigrees)) {?>
			<table class="grid">
				<thead>
					<tr>
						<th><?php echo CHtml::link('Hospital no',$this->getUri(array('sortby'=>'hos_num')))?></th>
						<th><?php echo CHtml::link('Title',$this->getUri(array('sortby'=>'title')))?></th>
						<th><?php echo CHtml::link('Patient name',$this->getUri(array('sortby'=>'patient_name')))?></th>
						<th><?php echo CHtml::link('Gender',$this->getUri(array('sortby'=>'gender')))?></th>
						<th><?php echo CHtml::link('Pedigree gene',$this->getUri(array('sortby'=>'gene')))?></th>
						<th><?php echo CHtml::link('Pedigree diagnosis',$this->getUri(array('sortby'=>'diagnosis')))?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ($patient_pedigrees as $i => $patient_pedigree) {?>
						<tr class="clickable" data-uri="<?php echo Yii::app()->createUrl('/patient/view/'.$patient_pedigree->patient_id)?>">
							<td><?php echo $patient_pedigree->patient->hos_num?></td>
							<td><?php echo $patient_pedigree->patient->title?>
							<td><?php echo strtoupper($patient_pedigree->patient->last_name)?>, <?php echo $patient_pedigree->patient->first_name?></td>
							<td><?php echo $patient_pedigree->patient->gender?>
							<td><?php echo $patient_pedigree->pedigree->gene ? $patient_pedigree->pedigree->gene->name : 'None'?>
							<td><?php echo $patient_pedigree->pedigree->disorder ? $patient_pedigree->pedigree->disorder->term : 'None'?>
						</tr>
					<?php }?>
				</tbody>
				<tfoot class="pagination-container">
					<tr>
						<td colspan="8">
							<?php echo $this->renderPartial('_pagination',array(
								'page' => $page,
								'pages' => $pages,
							))?>
						</td>
					</tr>
				</tfoot>
			</table>
		<?php }?>
	</form>
</div>
