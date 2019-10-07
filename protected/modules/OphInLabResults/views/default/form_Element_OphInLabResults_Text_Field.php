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
                        <?php echo $form->textArea($element, 'result');?>
                    </td>
                </tr>
                <?php if ($element->resultType->show_units) { ?>
                <tr>
                    <td>
                        <?php $element->setDefaultUnit();?>
                        <?= $form->textField($element, 'unit', ['disabled' => ($element->resultType->allow_unit_change ? '' : 'disabled') ]); ?>
                    </td>
                </tr>
                <?php } ?>
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