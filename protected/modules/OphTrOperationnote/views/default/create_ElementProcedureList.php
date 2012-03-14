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

<h2>Procedure list:</h2>

<p>This is the content of the procedure list element type create view.	You can customise this page by editing <tt><?php echo __FILE__; ?></tt></p>

<div id="anaestheticType" class="eventDetail">
	<div class="label"><?php echo CHtml::encode($element->getAttributeLabel('anaesthetic_type')); ?>:</div>
	<div class="data">
		<?php foreach ($element->getAnaestheticOptions() as $id => $value) {?>
			<span class="group">
				<input id="ElementProcedureList_anaesthetic_type_<?php echo $id?>" <?php if ($element->anaesthetic_type == $id){?>checked="checked" <?php }?>value="<?php echo $id?>" type="radio" name="ElementProcedureList[anaesthetic_type]" />
				<label for="ElementProcedureList_anaesthetic_type_<?php echo $id?>"><?php echo $value?></label>
			</span>
		<?php }?>
	</div>
</div>

<?php echo BaseEventTypeCHtml::renderDropDownList($element, 'ElementProcedureList[surgeon_id]', $element->surgeon_id, BaseEventTypeCHtml::listData(Contact::model()->findAll(), 'id', 'FullName')); ?>
<?php echo BaseEventTypeCHtml::renderDropDownList($element, 'ElementProcedureList[assistant_id]', $element->assistant_id, BaseEventTypeCHtml::listData(Contact::model()->findAll(), 'id', 'FullName')); ?>

