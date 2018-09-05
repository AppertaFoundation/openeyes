<?php $cvi_status = PatientOphInfoCviStatus::model()->active()->findAll(array('order' => 'display_order'));
$cvi_status_list = array();
foreach ($cvi_status as $item) {
  $cvi_status_list[$item->id] = $item->name;
}
?>
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
                <?php echo $form->radioButtons($element, 'cvi_status_id', $cvi_status_list,
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
