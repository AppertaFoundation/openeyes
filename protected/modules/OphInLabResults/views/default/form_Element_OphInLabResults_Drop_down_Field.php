<section id="result-output" class="element-fields">
    <div class="element-fields">
        <div class="active-form">
            <table class="cols-11">
                <tbody>
                <tr>
                    <td>
                        <?= $form->hiddenInput($element, 'type');?>
                        <?php echo $form->textField($element, 'time', $element->getHtmlOptionsForInput('time'), array());?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo $form->dropDownList($element, 'result',
                            CHtml::listData(
                                OphInLabResults_Type_Options::model()->findAll(array(
                                    'condition' => 'type = ' . $element->type,
                                )),
                                'value', 'value'), array('empty' => 'Select'), false);?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php $element->setDefaultUnit()?>
                        <?php echo $form->textField($element, 'unit'); ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo $form->textArea($element, 'comment', $element->getHtmlOptionsForInput('comment'), array(), ['maxlength'=>'250']);?>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>