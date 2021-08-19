<div class="element-fields full-width flex-layout flex-top col-gap" xmlns="http://www.w3.org/1999/html">
    <div class="cols-6 data-group">
      <table>
        <colgroup>
          <col class="cols-5">
          <col class="cols-7">
        </colgroup>
        <tbody>
        <tr>
          <td>
              <?php echo $element->getAttributeLabel('title_surname')?>
          </td>
          <td>
                <?php echo $form->textField($element, 'title_surname', array('nowrapper'=>true, 'class'=>'cols-full'), array(), array()) ?>
          </td>
        </tr>
        <tr>
          <td>
                <?php echo $element->getAttributeLabel('other_names')?>
          </td>
          <td>
                <?php echo $form->textField($element, 'other_names', array('nowrapper'=>true, 'class'=>'cols-full'), array(), array()) ?>
          </td>
        </tr>
        <tr>
          <td>
                <?php echo $element->getAttributeLabel('address')?>
          </td>
          <td>
                <?php echo $form->textArea($element, 'address', array('nowrapper'=>true, 'class'=>'cols-full'), false, array('rows' => 4), array()) ?>
          </td>
        </tr>
        <tr>
          <td>
                <?php echo $element->getAttributeLabel('postcode')?>
          </td>
          <td>
                <?php echo $form->textField($element, 'postcode', array('nowrapper'=>true, 'class'=>'cols-full'), array(), array()) ?>
          </td>
        </tr>
        <tr>
          <td>
                <?php echo $element->getAttributeLabel('email')?>
          </td>
          <td>
                <?php echo $form->textField($element, 'email', array('nowrapper'=>true, 'class'=>'cols-full'), array(), array()) ?>
          </td>
        </tr>
        <tr>
          <td>
                <?php echo $element->getAttributeLabel('telephone')?>
          </td>
          <td>
                <?php echo $form->textField($element, 'telephone', array('nowrapper'=>true, 'class'=>'cols-full'), array(), array()) ?>
          </td>
        </tr>
        <tr>
          <td>
                <?php echo $element->getAttributeLabel('date_of_birth')?>
          </td>
          <td>
                <?php echo $form->datePicker(
                    $element,
                    'date_of_birth',
                    array(),
                    array(
                      'nowrapper'=>true,
                      'class'=>'cols-full'),
                    array()
                ) ?>
          </td>
        </tr>
        <tr>
          <td>
                <?php echo $element->getAttributeLabel('gender_id')?>
          </td>
          <td>
                <?php echo $form->dropDownList(
                    $element,
                    'gender_id',
                    CHtml::listData(
                        Gender::model()->findAll(),
                        'id',
                        'name'
                    ),
                    array(
                      'empty' => 'Select',
                      'nowrapper'=>true,
                      'class'=>'cols-full'),
                    false,
                    array()
                ) ?>
          </td>
        </tr>
        <tr>
          <td>
                <?php echo $element->getAttributeLabel('ethnic_group_id')?>
          </td>
          <td>
                <?php echo $form->dropDownList(
                    $element,
                    'ethnic_group_id',
                    CHtml::listData(
                        EthnicGroup::model()->findAll(),
                        'id',
                        'name'
                    ),
                    array(
                      'empty' => 'Select',
                      'nowrapper'=>true,
                      'class'=>'cols-full'),
                    false,
                    array('label' => 4, 'field' => 8)
                ) ?>
          </td>
        </tr>
        </tbody>
      </table>
    </div>
    <div class="cols-6 data-group">
      <table>
        <colgroup>
          <col class="cols-5">
          <col class="cols-7">
        </colgroup>
        <tbody>
          <tr>
            <td>
                <?php echo $element->getAttributeLabel('nhs_number')?>
            </td>
            <td>
                <?php echo $form->textField($element, 'nhs_number', array('nowrapper'=>true, 'class'=>'cols-full'), array(), array()) ?>
            </td>
          </tr>
        <tr>
          <td>
                <?php echo $element->getAttributeLabel('gp_name')?>
          </td>
          <td>
                <?php echo $form->textField($element, 'gp_name', array('nowrapper'=>true, 'class'=>'cols-full'), array(), array()) ?>
          </td>
        </tr>
        <tr>
          <td>
                <?php echo $element->getAttributeLabel('gp_address')?>
          </td>
          <td>
                <?php echo $form->textArea($element, 'gp_address', array('nowrapper'=>true, 'class'=>'cols-full'), false, array('rows' => 4), array()) ?>
          </td>
        </tr>
        <tr>
          <td>
                <?php echo $element->getAttributeLabel('gp_telephone')?>
          </td>
          <td>
                <?php echo $form->textField($element, 'gp_telephone', array('nowrapper'=>true, 'class'=>'cols-full'), array(), array()) ?>
          </td>
        </tr>
        <tr>
          <td>
              Find Local Authority Details
          </td>
          <td>
                <?php $this->renderPartial('localauthority_search', array('class'=>'cols-full')); ?>
          </td>
        </tr>
        <tr>
          <td>
                <?php echo $element->getAttributeLabel('la_name')?>
          </td>
          <td>
                <?php echo $form->textField($element, 'la_name', array('nowrapper'=>true, 'class'=>'cols-full'), array(), array()) ?>
          </td>
        </tr>
        <tr>
          <td>
                <?php echo $element->getAttributeLabel('la_address')?>
          </td>
          <td>
                <?php echo $form->textArea($element, 'la_address', array('nowrapper'=>true, 'class'=>'cols-full'), false, array('rows' => 4), array()) ?>
          </td>
        </tr>
        <tr>
          <td>
                <?php echo $element->getAttributeLabel('la_telephone')?>
          </td>
          <td>
                <?php echo $form->textField($element, 'la_telephone', array('nowrapper'=>true, 'class'=>'cols-full'), array(), array()) ?>
          </td>
        </tr>
        </tbody>
      </table>
    </div>
</div>
