<?php
$selected_firm_id = Yii::app()->session['selected_firm_id'];
$selected_subspecialty_id = $element->to_subspecialty_id != "" ?
    $element->to_subspecialty_id :
    \Firm::model()->findByPk($selected_firm_id)->getSubspecialtyID();
?>

<div class="required internal-referral-section">
    <hr class="divider"/>
    <h3>Internal Referral to:</h3>
    <div class="data-group">
        <table class="cols-full">
            <colgroup>
                <col class="cols-5">
                <col class="cols-7">
            </colgroup>
            <tbody>
            <tr>
                <td>Service</td>
                <td>
                    <?php
                    $element->to_subspecialty_id = $selected_subspecialty_id;

                    $criteria = new CDbCriteria();
                    $criteria->with = ['serviceSubspecialtyAssignment' =>
                        ['with' => 'firms']
                    ];
                    $criteria->order = 't.name';
                    $criteria->addCondition('firms.active = 1'); ?>

                    <?= \CHtml::activeDropDownList(
                        $element,
                        "to_subspecialty_id",
                        CHtml::listData(Subspecialty::model()->findAll($criteria), 'id', 'name'),
                        array('empty' => '- None -', 'class' => 'cols-full')
                    ) ?>
                </td>
            </tr>
            <tr>
                <td>Location</td>
                <td>
                    <?php
                    $site_id = Yii::app()->session['selected_site_id'];
                    if (!$element->to_location_id) {
                        $to_location = OphCoCorrespondence_InternalReferral_ToLocation::model()->findByAttributes(array('site_id' => $site_id));
                        $element->to_location_id = $to_location ? $to_location->id : null;
                    }
                    echo CHtml::activeDropDownList(
                        $element,
                        "to_location_id",
                        $element->getToLocations(true),
                        array('empty' => '- None -', 'class' => 'cols-full')
                    ) ?>
                </td>
            </tr>
            <tr>
                <td><?= Firm::contextLabel() ?></td>
                <td>
                    <?php
                    $only_service_firms = SettingMetadata::checkSetting('filter_service_firms_internal_referral', 'on');
                    $applicable_firms = InternalReferralSiteFirmMapping::findInternalReferralFirms($element->to_location_id, $selected_subspecialty_id, $only_service_firms);

                    echo \CHtml::activeDropDownList(
                        $element,
                        "to_firm_id",
                        $applicable_firms,
                        array('empty' => '- None -', 'class' => 'cols-full')
                    ); ?>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="align-right">
                    <?php
                    $this->widget('application.widgets.RadioButtonList', array(
                        'element' => $element,
                        'name' => CHtml::modelName($element) . "[is_same_condition]",
                        'label_above' => false,
                        'field_value' => false,
                        'field' => 'is_same_condition',
                        'data' => array(
                            1 => 'Same Condition',
                            0 => 'Different',
                        ),
                        'htmlOptions' => array(
                            'nowrapper' => true,
                        ),
                        'selected_item' => $element->is_same_condition ? $element->is_same_condition : 0,
                    )); ?>
                </td>
            </tr>
            <tr>
                <td>
                    Flag
                </td>
                <td>
                    <label class="inline">
                        <?= \CHtml::activeCheckBox($element, 'is_urgent'); ?>
                        <?php echo $element->getAttributeLabel('is_urgent'); ?>
                    </label>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
