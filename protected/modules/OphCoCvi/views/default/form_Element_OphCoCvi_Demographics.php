<div class="element-fields row" xmlns="http://www.w3.org/1999/html">
    <div class="large-6 column">
        <?php echo $form->textField($element, 'title_surname', array(), array(), array('label' => 4, 'field' => 8)) ?>
        <?php echo $form->textField($element, 'other_names', array(), array(), array('label' => 4, 'field' => 8)) ?>
        <?php echo $form->textArea($element, 'address', array(), false, array('rows' => 4), array('label' => 4, 'field' => 8)) ?>
        <?php echo $form->textField($element, 'postcode', array(), array(), array('label' => 4, 'field' => 4)) ?>
        <?php echo $form->textField($element, 'email', array(), array(), array('label' => 4, 'field' => 8)) ?>
        <?php echo $form->textField($element, 'telephone', array(), array(), array('label' => 4, 'field' => 8)) ?>
        <?php echo $form->datePicker($element, 'date_of_birth', array(), array(), array('label' => 4, 'field' => 8)) ?>
        <?php echo $form->dropDownList($element, 'gender_id', CHtml::listData(Gender::model()->findAll(array("condition"=>"version =  0")), 'id', 'name'), array('empty' => '- Please Select -'), false, array('label' => 4, 'field' => 8)) ?>
        <?php echo $form->dropDownList($element, 'ethnic_group_id', CHtml::listData(EthnicGroup::model()->findAll(array("condition"=>"version =  0")), 'id', 'name'), array('empty' => '- Please Select -'), false, array('label' => 4, 'field' => 8)) ?>
    </div>
    <div class="large-6 column">
        <?php echo $form->textField($element, 'nhs_number', array(), array(), array('label' => 4, 'field' => 8)) ?>
        <?php echo $form->textField($element, 'gp_name', array(), array(), array('label' => 4, 'field' => 8)) ?>
        <?php echo $form->textArea($element, 'gp_address', array(), false, array('rows' => 4), array('label' => 4, 'field' => 8)) ?>
        <?php echo $form->textField($element, 'gp_telephone', array(), array(), array('label' => 4, 'field' => 8)) ?>
        <hr />
        <?php
        $hide_search = strlen($element->la_name) > 0;
        ?>
        <div class="row field-row">
            <div class="small-push-6 column-5"><a href="#" id="la-search-toggle" class="button secondary small<?= $hide_search ? '': ' disabled'?>">Find Local Authority Details</a></div>
        </div>
        <?php $this->renderPartial('localauthority_search', array('hidden' => $hide_search)); ?>
        <?php echo $form->textField($element, 'la_name', array(), array(), array('label' => 4, 'field' => 8)) ?>
        <?php echo $form->textArea($element, 'la_address', array(), false, array('rows' => 4), array('label' => 4, 'field' => 8)) ?>
        <?php echo $form->textField($element, 'la_telephone', array(), array(), array('label' => 4, 'field' => 8)) ?>
    </div>
</div>
