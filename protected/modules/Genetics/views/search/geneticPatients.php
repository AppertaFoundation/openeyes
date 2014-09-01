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
								<th>Subject ID:</th>
								<th>MEH Number:</th>
								<th>Pedigree ID:</th>
							</tr>
							</thead>
							<tbody>
							<tr>
								<td>
									<?php echo CHtml::textField('subject-id', @$_GET['subject-id'], array('placeholder' => 'Subject ID'))?>
								</td>
								<td>
									<?php echo CHtml::textField('meh-number', @$_GET['meh-number'], array('placeholder' => 'MEH Number'))?>
								</td>
								<td>
									<?php echo CHtml::textField('pedigree-id', @$_GET['pedigree-id'], array('placeholder' => 'Pedigree ID'))?>
								</td>
								<td>
									<button id="search_genetics_patients" class="secondary" type="submit">
										Search
									</button>
								</td>
							</tr>
							<thead>
							<tr>
								<th>Firstname:</th>
								<th>Surname:</th>
								<th>Age or DOB:</th>
							</tr>
							</thead>
							<tr>
								<td>
									<?php echo CHtml::textField('first-name', @$_GET['first-name'], array('placeholder' => 'First name'))?>
								</td>
								<td>
									<?php echo CHtml::textField('last-name', @$_GET['last-name'], array('placeholder' => 'Last name'))?>
								</td>
								<td>
									<?php echo CHtml::textField('dob', @$_GET['dob'], array('placeholder' => 'Age or DOB'))?>
								</td>
								<td>

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

		<?php if (count($patients) <1) {?>
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

		<?php if (!empty($patients)) {?>
			<table class="grid">
				<thead>
					<tr>
						<th><?php echo CHtml::link('Hospital no',$this->getUri(array('sortby'=>'hos_num')))?></th>
						<th><?php echo CHtml::link('Title',$this->getUri(array('sortby'=>'title')))?></th>
						<th><?php echo CHtml::link('Patient name',$this->getUri(array('sortby'=>'patient_name')))?></th>
						<th><?php echo CHtml::link('Gender',$this->getUri(array('sortby'=>'gender')))?></th>
						<th><?php echo CHtml::link('DOB',$this->getUri(array('sortby'=>'dob')))?></th>
						<th><?php echo CHtml::link('Year',$this->getUri(array('sortby'=>'yob')))?></th>
						<th><?php echo CHtml::link('Status',$this->getUri(array('sortby'=>'status')))?></th>
						<th><?php echo CHtml::link('Family',$this->getUri(array('sortby'=>'pedigree_id')))?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ($patients as $patient) {?>
						<tr class="clickable" data-uri="<?php echo Yii::app()->createUrl('/patient/view/'.$patient['id'])?>">
							<td><?php echo $patient['hos_num']?></td>
							<td><?php echo $patient['title']?>
							<td><?php echo strtoupper($patient['last_name'])?>, <?php echo $patient['first_name']?></td>
							<td><?php echo $patient['gender']?></td>
							<td><?php echo $patient['dob']?></td>
							<td><?php echo $patient['yob']?></td>
							<td><?php echo $patient['name']?></td>
							<td><?php echo $patient['pedigree_id']?></td>
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
