
<div class="element-fields">

	<?php echo $form->textField($element, 'target_refraction_'.$side, null, null, array('label'=>4, 'field'=>2))?>

	<?php //echo $form->dropDownList($element, 'formula_id_'.$side, CHtml::listData(OphInBiometry_Calculation_Formula::model()->findAll(array('order'=> 'display_order asc')),'id','name'),array('empty'=>'- Please select -'),null,array('label'=>3, 'field'=>6))?>
	<!--
	<div class="row">
		<div class="large-8 column">
			<table name="table" id="iol-table_<?php echo $side?>" align="center" cellspacing="0" width="200" style="margin-top: 10px">
				<thead>
				<tr>
					<td align="left" width="60%"><h4 style="margin-left: 4px">IOL power</h4></td>
					<td align="right" width="40%"><h4>Refraction</h4></td>
				</tr>
				</thead>
				<tbody id="tableBody_<?php echo $side?>">
				</tbody>
			</table>
		</div>
	</div>
	-->
</div>
