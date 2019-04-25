<section id="result-output" class="element-fields">
    <div class="element-fields">
        <div class="active-form">
            <table class="cols-11">
                <tbody>
                <tr>
                    <?= $form->hiddenInput($element, 'type');?>
                    <?php echo $form->textField($element, 'time', $element->getHtmlOptionsForInput('time'), array());?>
                </tr>
                <tr>
                    <?php echo $form->numberField($element, 'result');?>
                </tr>
                <tr>
                    <?php echo $form->textArea($element, 'comment', $element->getHtmlOptionsForInput('comment'), array(), ['maxlength'=>'250']);?>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>