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
	<h2>Advanced Patient Search</h2>

	<div class="large-12 column">
		<?php
		$form = $this->beginWidget('BaseEventTypeCActiveForm',array(
			'id' => 'searchform',
			'enableAjaxValidation' => false,
			'focus' => '#search',
			'action' => Yii::app()->createUrl('/Genetics/search/geneticPatients'),
			'method' => 'GET',
		))?>
		<div class="large-12 column">
			<div class="panel">
				<div class="row">
					<div class="large-12 column">
						<table class="grid">
							<thead>
							<tr>
								<th>First name:</th>
								<th>Last name:</th>
								<th>Pedigree ID:</th>
							</tr>
							</thead>
							<tbody>
							<tr>
								<td>
									<?php echo CHtml::textField('first-name', @$_GET['first-name'], array('placeholder' => 'First name'))?>
									<br />
									<?php echo CHtml::checkBox('part_first_name',false) ?> Search part word
								</td>
								<td>
									<?php echo CHtml::textField('last-name', @$_GET['last-name'], array('placeholder' => 'Last name'))?>
									<br />
									<?php echo CHtml::checkBox('part_last_name',false) ?> Search part word
								</td>
								<td>
									<?php echo CHtml::textField('pedigree-id', @$_GET['pedigree-id'], array('placeholder' => 'Pedigree ID'))?>
									<br /><br />
								</td>
								<td>
									<button id="search_genetics_patients" class="secondary" type="submit">
										Search
									</button>
								</td>
							</tr>
							<thead>
							<tr>
								<th>Age or DOB:</th>
								<th colspan="2">Comments</th>
							</tr>
							</thead>
							<tr>
								<td>
									<?php echo CHtml::textField('dob', @$_GET['dob'], array('placeholder' => 'Age or DOB'))?>
								</td>
								<td colspan="2">
									<?php echo CHtml::textField('comments', @$_GET['comments'], array('placeholder' => 'Comments'))?>
								</td>
							</tr>
							<tr>
								<td colspan="3">
									<?php $form->widget('application.widgets.DiagnosisSelection',array(
										'value' => @$_GET['disorder-id'],
										'field' => 'principal_diagnosis',
										'options' => CommonOphthalmicDisorder::getList(Firm::model()->findByPk($this->selectedFirmId)),
										'layoutColumns' => array(
											'label' => $form->layoutColumns['label'],
											'field' => 4,
										),
										'default' => false,
										'htmlOptions' => array(
											'fieldLabel' => 'Principal diagnosis',
										),
									))?>
								</td>
							</tr>
							<?php /*
							<tr>
								<td colspan="4">
									<?php $form->widget('application.widgets.DiagnosisSelection',array(
										'value' => @$_GET['disorder-id'],
										'field' => 'principal_diagnosis',
										'options' => CommonOphthalmicDisorder::getList(Firm::model()->findByPk($this->selectedFirmId)),
										'layoutColumns' => array(
											'label' => $form->layoutColumns['label'],
											'field' => 4,
										),
										'default' => false,
										'htmlOptions' => array(
											'fieldLabel' => 'Principal diagnosis',
										),
									))?>
								</td>
							</tr> */
							?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<?php $this->endWidget()?>
	</div>

	<h2>Genetics patients</h2>

	<form id="admin_sequences">
		<input type="hidden" id="select_all" value="0" />

		<?php if (count($results) <1) {?>
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

		<?php if (!empty($results)) {?>
			<table class="grid">
				<thead>
					<tr>
						<th><?php echo CHtml::link('Hospital no',$this->getUri(array('sortby'=>'hos_num')))?></th>
						<th><?php echo CHtml::link('Patient name',$this->getUri(array('sortby'=>'patient_name')))?></th>
						<th><?php echo CHtml::link('Gender',$this->getUri(array('sortby'=>'gender')))?></th>
						<th><?php echo CHtml::link('DOB',$this->getUri(array('sortby'=>'dob')))?></th>
						<th><?php echo CHtml::link('Year',$this->getUri(array('sortby'=>'yob')))?></th>
						<th><?php echo CHtml::link('Family',$this->getUri(array('sortby'=>'pedigree_id')))?></th>
						<th><?php echo CHtml::link('Comments',$this->getUri(array('sortby'=>'comments')))?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ($results as $result) {?>
						<tr class="clickable" data-uri="<?php echo Yii::app()->createUrl('/patient/view/'.$result['id'])?>">
							<td><?php echo $result['hos_num']?></td>
							<td><?php echo strtoupper($result['last_name'])?>, <?php echo $result['first_name']?></td>
							<td><?php echo $result['gender']?></td>
							<td><?php echo $result['dob']?></td>
							<td><?php echo $result['yob']?></td>
							<td><?php echo $result['pedigree_id']?></td>
							<td class="large-5"><?php echo $result['comments']?></td>
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
