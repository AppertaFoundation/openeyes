<div class="element-fields row" xmlns="http://www.w3.org/1999/html">
    <div class="large-6 column">
        <?php echo $form->textField($element, 'title_surname', array(), array(), array('label' => 4, 'field' => 8)) ?>
        <?php echo $form->textField($element, 'other_names', array(), array(), array('label' => 4, 'field' => 8)) ?>
        <?php echo $form->textArea($element, 'address', array(), false, array('rows' => 4), array('label' => 4, 'field' => 8)) ?>
        <div class="row field-row">
            <div class="large-4 column">
                <label for="OEModule_OphCoCvi_models_Element_OphCoCvi_Demographics_V1_postcode">
                    <?php echo CHtml::encode($element->getAttributeLabel('postcode')); ?>:
                </label>
            </div>
            <div class="large-4 column">
                <div id="div_OEModule_OphCoCvi_models_Element_OphCoCvi_Demographics_V1_postcode" class="row">
                    <div class="large-12 column">
                        <input maxlength="4" autocomplete="off" type="text" value="<?= CHtml::encode($element->postcode) ?>" name="OEModule_OphCoCvi_models_Element_OphCoCvi_Demographics_V1[postcode]" id="OEModule_OphCoCvi_models_Element_OphCoCvi_Demographics_V1_postcode"> </div>
                </div>
            </div>
            <div class="large-4 column">
                <div id="div_OEModule_OphCoCvi_models_Element_OphCoCvi_Demographics_V1_postcode_2nd" class="row">
                    <div class="large-12 column">
                        <input maxlength="3" autocomplete="off" type="text" value="<?= CHtml::encode($element->postcode_2nd) ?>" name="OEModule_OphCoCvi_models_Element_OphCoCvi_Demographics_V1[postcode_2nd]" id="OEModule_OphCoCvi_models_Element_OphCoCvi_Demographics_V1_postcode_2nd"> </div>
                </div>
            </div>
        </div>
        <?php
            $ethnicGroup = EthnicGroup::model()->findAll(array("condition" => "version =  1"));
            $ethnicGroupData = array();
            foreach($ethnicGroup as $row){
                $ethnicGroupData[$row->id] =  array('data-describe' => $row->describe_needs );
            }
        ?>
        <?php echo $form->textField($element, 'email', array(), array(), array('label' => 4, 'field' => 8)) ?>
        <?php echo $form->textField($element, 'telephone', array(), array(), array('label' => 4, 'field' => 8)) ?>
        <?php echo $form->datePicker($element, 'date_of_birth', array(), array(), array('label' => 4, 'field' => 8)) ?>
        <?php echo $form->dropDownList($element, 'gender_id', CHtml::listData(Gender::model()->findAll(array("condition" => "version =  1")), 'id', 'name'), array('empty' => '- Please Select -'), false, array('label' => 4, 'field' => 8)) ?>
        <?php echo $form->dropDownList($element, 'ethnic_group_id', 
                CHtml::listData($ethnicGroup, 'id', 'name'), 
                array(
                    'empty' => '- Please Select -', 
                    'options' => $ethnicGroupData
                ), 
                false, 
                array('label' => 4, 'field' => 8)) ?>
        <?php echo $form->textArea($element, 'describe_ethnics',  array(), false, array('rows' => 2), array('label' => 4, 'field' => 8)) ?>
    </div>
    <div class="large-6 column">
        <?php echo $form->textField($element, 'nhs_number', array(), array(), array('label' => 4, 'field' => 8)) ?>
        <?php echo $form->textField($element, 'gp_name', array(), array(), array('label' => 4, 'field' => 8)) ?>
        <?php echo $form->textArea($element, 'gp_address', array(), false, array('rows' => 4), array('label' => 4, 'field' => 8)) ?>
        <div class="row field-row">
            <div class="large-4 column">
                <label for="OEModule_OphCoCvi_models_Element_OphCoCvi_Demographics_V1_gp_postcode">
                    <?php echo CHtml::encode($element->getAttributeLabel('gp_postcode')); ?>:
                </label>
            </div>
            <div class="large-4 column">
                <div id="div_OEModule_OphCoCvi_models_Element_OphCoCvi_Demographics_V1_gp_postcode" class="row">
                    <div class="large-12 column">
                        <input maxlength="4" autocomplete="off" type="text" value="<?= CHtml::encode($element->gp_postcode) ?>" name="OEModule_OphCoCvi_models_Element_OphCoCvi_Demographics_V1[gp_postcode]" id="OEModule_OphCoCvi_models_Element_OphCoCvi_Demographics_V1_gp_postcode"> </div>
                </div>
            </div>
            <div class="large-4 column">
                <div id="div_OEModule_OphCoCvi_models_Element_OphCoCvi_Demographics_V1_gp_postcode_2nd" class="row">
                    <div class="large-12 column">
                        <input maxlength="3" autocomplete="off" type="text" value="<?= CHtml::encode($element->gp_postcode_2nd) ?>" name="OEModule_OphCoCvi_models_Element_OphCoCvi_Demographics_V1[gp_postcode_2nd]" id="OEModule_OphCoCvi_models_Element_OphCoCvi_Demographics_V1_gp_postcode_2nd"> </div>
                </div>
            </div>
        </div>

        <?php echo $form->textField($element, 'gp_telephone', array(), array(), array('label' => 4, 'field' => 8)) ?>
        <hr />
        <?php
        $hide_search = strlen($element->la_name) > 0;
        ?>
        <div class="row field-row">
            <div class="small-push-6 column-5"><a href="#" id="la-search-toggle" class="button secondary small<?= $hide_search ? '' : ' disabled' ?>">Find Local Authority Details</a></div>
        </div>
        <?php $this->renderPartial('localauthority_search', array('hidden' => $hide_search)); ?>
        <?php echo $form->textField($element, 'la_name', array(), array(), array('label' => 4, 'field' => 8)) ?>
        <?php echo $form->textArea($element, 'la_address', array(), false, array('rows' => 4), array('label' => 4, 'field' => 8)) ?>
        <div class="row field-row">
            <div class="large-4 column">
                <label for="OEModule_OphCoCvi_models_Element_OphCoCvi_Demographics_V1_la_postcode">
                    <?php echo CHtml::encode($element->getAttributeLabel('la_postcode')); ?>:
                </label>
            </div>
            <div class="large-4 column">
                <div id="div_OEModule_OphCoCvi_models_Element_OphCoCvi_Demographics_V1_la_postcode" class="row">
                    <div class="large-12 column">
                        <input maxlength="4" autocomplete="off" type="text" value="<?= CHtml::encode($element->la_postcode) ?>" name="OEModule_OphCoCvi_models_Element_OphCoCvi_Demographics_V1[la_postcode]" id="OEModule_OphCoCvi_models_Element_OphCoCvi_Demographics_V1_la_postcode"> </div>
                </div>
            </div>
            <div class="large-4 column">
                <div id="div_OEModule_OphCoCvi_models_Element_OphCoCvi_Demographics_V1_la_postcode_2nd" class="row">
                    <div class="large-12 column">
                        <input maxlength="3" autocomplete="off" type="text" value="<?= CHtml::encode($element->la_postcode_2nd) ?>" name="OEModule_OphCoCvi_models_Element_OphCoCvi_Demographics_V1[la_postcode_2nd]" id="OEModule_OphCoCvi_models_Element_OphCoCvi_Demographics_V1_la_postcode_2nd"> </div>
                </div>
            </div>
        </div>
        <?php echo $form->textField($element, 'la_telephone', array(), array(), array('label' => 4, 'field' => 8)) ?>
        <?php echo $form->textField($element, 'la_email', array(), array(), array('label' => 4, 'field' => 8)) ?>


    </div>
</div>