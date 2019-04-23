<section id="result-output" class="element-fields">
    <div class="element-fields">
        <div class="active-form">
            <table class="cols-11">
                <tbody>
                <tr>
                    <?php echo $form->datePicker($element, 'time', $element->getHtmlOptionsForInput('time'), array());?>
                </tr>
                <tr>
                    <?php echo $form->textField($element, 'result', $element->getHtmlOptionsForInput('result'), array());?>
                </tr>
                <tr>
                    <?php echo $form->textArea($element, 'comment', $element->getHtmlOptionsForInput('comment'), array(), ['maxlength'=>'250']);?>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>
