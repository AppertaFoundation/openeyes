<div class="cols-10">
    <?= $form->hiddenField($element, 'booking_event_id') ?>
    <table class="cols-full large-text">
        <colgroup>
            <col class="cols-3">
        </colgroup>
        <tbody>
            <?php foreach ($element->procedure_assignments as $i => $procedure) {
                $eye_id = (int)$procedure->eye_id;
                ?>
                    <tr>
                        <?=CHtml::hiddenField("{$name}[procedure_assignments][$i][proc_id]", $procedure->proc_id);?>
                        <?=CHtml::hiddenField("{$name}[procedure_assignments][$i][eye_id]", $eye_id);?>
                        <td>
                            <span class="oe-eye-lat-icons">
                                <i class="oe-i laterality <?=$eye_icons[$eye_id]['right']?> small pad"></i>
                                <i class="oe-i laterality <?=$eye_icons[$eye_id]['left']?> small pad"></i>
                            </span>
                        </td>
                        <td><?=$procedure->proc->term?></td>
                        <td>
                            <i class="oe-i-e i-TrOperation js-has-tooltip" data-tooltip-content="Procedure info from Op Booking"></i>
                        </td>
                    </tr>
            <?php }?>
            <tr>
                <?php foreach ($element->anaesthetic_type as $a_t) { ?>
                    <?=
                        CHtml::hiddenField("AnaestheticType[]", $a_t->id);
                    ?>
                <?php } ?>
                <th>Anaesthetic</th>
                <td><?=implode(' + ', $element->anaesthetic_type)?></td>
                <td></td>
            </tr>
        </tbody>
    </table>        
</div>
