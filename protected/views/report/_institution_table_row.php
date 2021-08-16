
<tr>
    <td>Institution</td>
    <td>
        <?php $institution_dropdown_disabled =  !Yii::app()->user->checkAccess('TaskReportAnyInstitution');
        $institution_dropdown_default_value = $institution_dropdown_disabled ?  Yii::app()->session['selected_institution_id'] : '';
        ?>

        <input type="hidden" name="<?=$field_name?>" value="<?=$institution_dropdown_default_value?>">
        <?= CHtml::dropDownList($field_name, $institution_dropdown_default_value, \CHtml::listData(Institution::model()->findAll(), 'id', 'name'),
            ['disabled' => $institution_dropdown_disabled ? 'disabled' : '',
                'empty' => 'All']) ?>

    </td>
</tr>
