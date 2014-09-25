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
	<h2>Sample Search</h2>

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
								<th>Date Taken From:</th>
								<th>Date Taken To:</th>
								<th>Sample Type:</th>
								<th>Comments:</th>
							</tr>
							</thead>
							<tbody>
							<tr>
								<td>
									<?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
										'name' => 'date-from',
										'id' => 'date-from',
										'options' => array(
											'showAnim'=>'fold',
											'dateFormat'=>Helper::NHS_DATE_FORMAT_JS
										),
										'value' => @$_GET['date-from'],
									))?>
								</td>
								<td>
									<?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
										'name' => 'date-to',
										'id' => 'date-to',
										'options' => array(
											'showAnim'=>'fold',
											'dateFormat'=>Helper::NHS_DATE_FORMAT_JS
										),
										'value' => @$_GET['date-to'],
									))?>
								</td>
								<td>
									<?php echo CHtml::dropDownList('sample-type',@$_GET['sample-type'],CHtml::listData(OphInBloodsample_Sample_Type::model()->findAll(array('order'=>'name asc')),'id','name'),array('empty' => '- All -'))?>
								</td>
								<td>
									<?php echo CHtml::textField('comment',@$_GET['comment'])?>
								</td>
								<td>
									<button id="search_blood_sample" class="secondary" type="submit">
										Search
									</button>
								</td>
							</tr>
						</tbody>
							</table>
						<table class="grid">
							<tbody>
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
										'allowClear' => true,
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

	<h2>DNA samples</h2>

	<form id="admin_sequences">
		<input type="hidden" id="select_all" value="0" />

		<?php if (count($results) <1) {?>
			<div class="alert-box no_results">
				<span class="column_no_results">
					<?php if (@$_GET['search']) {?>
						No results found for current criteria.
					<?php }else{?>
						Please enter criteria to search for samples.
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
						<th><?php echo CHtml::link('Date Taken',$this->getUri(array('sortby'=>'date_taken')))?></th>
						<th><?php echo CHtml::link('Sample Type',$this->getUri(array('sortby'=>'sample_type')))?></th>
						<th><?php echo CHtml::link('Volume',$this->getUri(array('sortby'=>'volume')))?></th>
						<th><?php echo CHtml::link('Comment',$this->getUri(array('sortby'=>'comment')))?></th>

					</tr>
				</thead>
				<tbody>
					<?php
					foreach ($results as $result) {?>
						<tr class="clickable" data-uri="<?php echo Yii::app()->createUrl('/OphInBloodsample/default/view/'.$result['id'])?>">
							<td><?php echo $result['hos_num']?></td>
							<td><?php echo strtoupper($result['last_name'])?>, <?php echo $result['first_name']?></td>
							<td><?php echo $result['event_date']?></td>
							<td><?php echo $result['name']?></td>
							<td><?php echo $result['volume']?></td>
							<td><?php echo $result['comments']?></td>
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
