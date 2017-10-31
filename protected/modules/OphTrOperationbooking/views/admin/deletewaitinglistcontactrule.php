<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<?php
/**
 * @todo : refactor the html
 */
?>
<div class="curvybox white">
	<div class="admin">
		<h3 class="georgia">Delete waiting list contact rule</h3>
		<?php echo $this->renderPartial('//admin/_form_errors', array('errors' => $errors))?>
		<div>
			<?php
            $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
                'id' => 'lcr_deleteform',
                'enableAjaxValidation' => false,
                'htmlOptions' => array('class' => 'sliding'),
                'focus' => '#contactname',
            ))?>
			<input type="hidden" name="delete" value="1" />
			<div>
				<span class="lcr_field"><?php echo $rule->getAttributeLabel('parent_rule_id')?>:</span>
				<span><?php echo $rule->parent ? $rule->parent->treeName : 'None'?></span>
			</div>
			<div>
				<span class="lcr_field"><?php echo $rule->getAttributeLabel('site_id')?>:</span>
				<span><?php echo $rule->site ? $rule->site->name : 'Not set'?></span>
			</div>
			<div>
				<span class="lcr_field"><?php echo $rule->getAttributeLabel('firm_id')?>:</span>
				<span><?php echo $rule->firm ? $rule->firm->name : 'Not set'?></span>
			</div>
			<div>
				<span class="lcr_field"><?php echo $rule->getAttributeLabel('service_id')?>:</span>
				<span><?php echo $rule->service ? $rule->service->name : 'Not set'?></span>
			</div>
			<div>
				<span class="lcr_field"><?php echo $rule->getAttributeLabel('name')?>:</span>
				<span><?php echo $rule->name?></span>
			</div>
			<div>
				<span class="lcr_field"><?php echo $rule->getAttributeLabel('telephone')?>:</span>
				<span><?php echo $rule->telephone?></span>
			</div>
			<?php $this->endWidget()?>
		</div>
		<?php if ($rule->children) {?>
			<div>
				<p style="font-size: 15px; margin: 0; padding: 0; margin-top: 10px; margin-bottom: 10px;"><strong><span style="color: #f00;">WARNING:</span> this rule has one or more descendants, if you proceed these will all be deleted.</strong></p>
				<?php
                $this->widget('CTreeView', array(
                    'data' => OphTrOperationbooking_Waiting_List_Contact_Rule::model()->findAllAsTree($rule, true, 'textPlain'),
                ))?>
			</div>
		<?php }?>
		<div>
			<p style="font-size: 15px; margin: 0; padding: 0; margin-top: 10px; margin-bottom: 10px;"><strong>Are you sure you want to delete this rule<?php if ($rule->children) {?> and its descendants<?php }?>?</strong></p>
		</div>
	</div>
</div>
<?php echo $this->renderPartial('//admin/_form_errors', array('errors' => $errors))?>
<div>
	<?php echo EventAction::button('Delete', 'delete', array('colour' => 'green'))->toHtml()?>
	<?php echo EventAction::button('Cancel', 'cancel', array('level' => 'cancel'))->toHtml()?>
	<img class="loader" src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif')?>" alt="loading..." style="display: none;" />
</div>
<script type="text/javascript">
	handleButton($('#et_cancel'),function() {
		window.location.href = baseUrl+'/OphTrOperationbooking/admin/edit'+OE_rule_model+'/<?php echo $rule->id?>';
	});
	handleButton($('#et_delete'),function() {
		$('#lcr_deleteform').submit();
	});
</script>
