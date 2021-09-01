<section id="result-output" class="element-fields">
    <div class="element-fields">
        <div class="active-form">
            <table class="standard cols-full">
                <colgroup>
                    <col class="cols-2">
                    <col class="cols-4">
                </colgroup>
                <tbody>
                <tr>
                    <td>
                        <?= $form->hiddenInput($element, 'type'); ?>
                        <?php echo $form->textField($element, 'time', ['type' => 'time']); ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="data-group flex-layout cols-full">
                            <div class="cols-2">Result</div>
                            <div class="cols-10">
                                <?php echo $form->numberField($element, 'result', ['step' => '0.1']); ?>
                                <span class="large-text highlighter orange js-lab-result-warning"
                                      style="<?php
                                        if (isset($element->result)&& $element->resultType->normal_min && $element->resultType->normal_min &&
                                          ($element->result > $element->resultType->normal_max || $element->result < $element->resultType->normal_min)) {
                                            echo "display:block";
                                        } else {
                                            echo "display:none";
                                        } ?>">
                    <?php if ($element->resultType->custom_warning_message) {
                        echo $element->resultType->custom_warning_message;
                    } else { ?>
                        The value is outside the normal range. Normal min: <?= $element->resultType->normal_min ?> Normal max: <?= $element->resultType->normal_max ?>
                    <?php } ?> </span>
                            </div>
                        </div>
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
                        <?php echo $form->textArea($element, 'comment', $element->getHtmlOptionsForInput('comment'), array(), ['maxlength' => '250']); ?>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>
