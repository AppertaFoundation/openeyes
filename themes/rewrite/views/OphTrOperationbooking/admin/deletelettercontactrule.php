<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

?>
<div class="box admin">
	<h2>Delete letter contact rule</h2>
	<?php echo $this->renderPartial('//admin/_form_errors',array('errors'=>$errors))?>
	<?php
	$form = $this->beginWidget('BaseEventTypeCActiveForm', array(
			'id'=>'lcr_deleteform',
			'enableAjaxValidation'=>false,
			'focus'=>'#contactname'
		))?>
	<input type="hidden" name="delete" value="1" />
	<div class="element-data">
		<div class="row data-row">
			<div class="large-2 column">
				<div class="data-label"><?php echo $rule->getAttributeLabel('parent_rule_id')?>:</div>
			</div>
			<div class="large-10 column">
				<div class="data-value"><?php echo $rule->parent ? $rule->parent->treeName : 'None'?></div>
			</div>
		</div>
		<div class="row data-row">
			<div class="large-2 column">
				<div class="data-label"><?php echo $rule->getAttributeLabel('site_id')?>:</div>
			</div>
			<div class="large-10 column">
				<div class="data-value"><?php echo $rule->site ? $rule->site->name : 'Not set'?></div>
			</div>
		</div>
		<div class="row data-row">
			<div class="large-2 column">
				<div class="data-label"><?php echo $rule->getAttributeLabel('firm_id')?>:</div>
			</div>
			<div class="large-10 column">
				<div class="data-value"><?php echo $rule->firm ? $rule->firm->name : 'Not set'?></div>
			</div>
		</div>
		<div class="row data-row">
			<div class="large-2 column">
				<div class="data-label"><?php echo $rule->getAttributeLabel('subspecialty_id')?>:</div>
			</div>
			<div class="large-10 column">
				<div class="data-value"><?php echo $rule->subspecialty ? $rule->subspecialty->name : 'Not set'?></div>
			</div>
		</div>
		<div class="row data-row">
			<div class="large-2 column">
				<div class="data-label"><?php echo $rule->getAttributeLabel('theatre_id')?>:</div>
			</div>
			<div class="large-10 column">
				<div class="data-value"><?php echo $rule->theatre ? $rule->theatre->name : 'Not set'?></div>
			</div>
		</div>
		<div class="row data-row">
			<div class="large-2 column">
				<div class="data-label"><?php echo $rule->getAttributeLabel('refuse_telephone')?>:</div>
			</div>
			<div class="large-10 column">
				<div class="data-value"><?php echo $rule->refuse_telephone ? $rule->refuse_telephone : 'Not set'?></div>
			</div>
		</div>
		<div class="row data-row">
			<div class="large-2 column">
				<div class="data-label"><?php echo $rule->getAttributeLabel('refuse_title')?>:</div>
			</div>
			<div class="large-10 column">
				<div class="data-value"><?php echo $rule->refuse_title ? $rule->refuse_title : 'Not set'?></div>
			</div>
		</div>
		<div class="row data-row">
			<div class="large-2 column">
				<div class="data-label"><?php echo $rule->getAttributeLabel('health_telephone')?>:</div>
			</div>
			<div class="large-10 column">
				<div class="data-value"><?php echo $rule->health_telephone ? $rule->health_telephone : 'Not set'?></div>
			</div>
		</div>
	</div>
	<?php $this->endWidget()?>


	<?php if ($rule->children) {?>
		<div>
			<p style="font-size: 15px; margin: 0; padding: 0; margin-top: 10px; margin-bottom: 10px;"><strong><span style="color: #f00;">WARNING:</span> this rule has one or more descendants, if you proceed these will all be deleted.</strong></p>
			<?php
			$this->widget('CTreeView',array(
					'data' => OphTrOperationbooking_Letter_Contact_Rule::model()->findAllAsTree($rule,true,'textPlain'),
				))?>
		</div>
	<?php }?>
</div>
<div>
	<p style="font-size: 15px; margin: 0; padding: 0; margin-top: 10px; margin-bottom: 10px;"><strong>Are you sure you want to delete this rule<?php if ($rule->children) {?> and its descendants<?php }?>?</strong></p>
</div>

<?php echo $this->renderPartial('//admin/_form_errors',array('errors'=>$errors))?>
<div>
	<?php echo EventAction::button('Delete', 'delete', array('level' => 'warning'), array('class' => 'button small'))->toHtml()?>
	<?php echo EventAction::button('Cancel', 'cancel', null, array('class' => 'button small'))->toHtml()?>
	<img class="loader" src="<?php echo Yii::app()->createUrl('/img/ajax-loader.gif')?>" alt="loading..." style="display: none;" />
</div>
<script type="text/javascript">
	handleButton($('#et_cancel'),function() {
		window.location.href = baseUrl+'/OphTrOperationbooking/admin/edit'+OE_rule_model+'/<?php echo $rule->id?>';
	});
	handleButton($('#et_delete'),function() {
		$('#lcr_deleteform').submit();
	});
</script>
