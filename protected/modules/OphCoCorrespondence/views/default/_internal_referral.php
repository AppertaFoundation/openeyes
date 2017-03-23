
<section class="element required internal-referral-section" style="border: 1px solid #fafafa;">

    <header class="element-header" style="background-color: #fafafa">

        <!-- Element title -->
        <h3 class="element-title">Internal Referral</h3>

    </header>

    <div class="element-fields">
        <div class="row">
            <div class="large-2 column">
                <label>To Service:* </label>
            </div>
            <div class="large-3 column">
                    <?php echo CHtml::activeDropDownList($element, "to_subspecialty_id",
                                    CHtml::listData(Subspecialty::model()->findAll(array('order' => 'name')), 'id', 'name'), array('empty' => '- None -')) ?>
            </div>

            <div class="large-1 column">&nbsp;</div>

            <div class="large-2 column">
                <label>For Consultant: </label>
            </div>
            <div class="large-3 column end">
                    <?php echo CHtml::activeDropDownList($element, "to_consultant_id",
                                CHtml::listData(User::model()->getAllConsultants(), 'id', 'name'), array('empty' => '- None -')) ?>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="large-2 column">&nbsp;</div>

            <div class="large-3 column end">
                <label class="inline">
                    <label>
                        <?php echo CHTML::activeCheckBox($element, 'is_urgent'); ?>
                        <?php echo $element->getAttributeLabel('is_urgent'); ?>
                    </label>
                </label>
            </div>

            <div class="large-1 column">&nbsp;</div>

            <div class="large-4 column end">

                <?php

                    $field = 'is_same_condition';
                    $this->widget('application.widgets.RadioButtonList', array(
                        'element' => $element,
                        'name' => CHtml::modelName($element)."[$field]",
                        'label_above' => false,
                        'field_value' => false,
                        'field' => $field,
                        'data' => array(
                                1 => 'Same Condition',
                                0 => 'Different Condition',
                        ),
                        'selected_item' => $element->$field,

                    ));

                ?>

            </div>

        </div>

    </div>

</section>