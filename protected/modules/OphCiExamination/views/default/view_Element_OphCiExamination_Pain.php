<?php
    $element_model_name = \CHtml::modelName($element);
?>

<div class="element-fields full-width">
    <!-- Chronologically sorted pain recordings -->
    <div class="cols-11">
        <table id="pain-entries-table" class="cols-full">
            <colgroup>
                <col class="cols-icon" span="12">
                <col class="cols-1"><!-- crying icon -->
            </colgroup>
            <thead>
                <tr>
                    <th></th>
                    <th class="center">0</th>
                    <th class="center">1</th>
                    <th class="center">2</th>
                    <th class="center">3</th>
                    <th class="center">4</th>
                    <th class="center">5</th>
                    <th class="center">6</th>
                    <th class="center">7</th>
                    <th class="center">8</th>
                    <th class="center">9</th>
                    <th class="center">10</th>
                    <th></th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Comments</th>
                    <th><!--trash, edit only --></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $entries = $element->entries;

                $row_count = 0;

                foreach ($entries as $entry) {
                    $entry = $entry->attributes;

                    $this->renderPartial(
                        'form_Element_OphCiExamination_Pain_Entry',
                        array('model' => $entry, 'element' => $element, 'row_count' => $row_count, 'editable' => false)
                    );
                    $row_count++;
                }
                ?>
            </tbody>
        </table>
    </div>
</div>