
<div class="element-fields flex-layout full-width">
    <table class="last-left cols-10">
        <colgroup>
            <col class="cols-2">
            <col class="cols-7">
            <col class="cols-3">
        </colgroup>

        <tbody>
        <tr>
            <td>
                CVI status
            </td>
            <td>
                <?php echo $form->radioButtons($element, 'cvi_status_id', array(
                    1 => $element::$UNKNOWN_STATUS,
                    3 => $element::$NOT_BLIND_STATUS,
                    4 => $element::$BLIND_STATUS,
                    5 => $element::$NOT_ELIGIBLE_STATUS,
                ),
                    $element->cvi_status_id,
                    false, false, false, false,
                    array('nowrapper' => true)
                ); ?>
            </td>
            <td>
                <?php
                echo $form->datePicker($element, 'element_date',
                    array('maxDate' => 'today'),
                    array(
                        'style' => 'margin-left:8px',
                        'nowrapper' => true,
                    ),
                    array(
                        'label' => 2,
                        'field' => 2,
                    )
                );
                ?>
            </td>
        </tr>
        </tbody>
    </table>
</div>
