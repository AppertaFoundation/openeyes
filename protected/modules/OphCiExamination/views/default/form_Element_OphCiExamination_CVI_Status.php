
<div class="element-fields flex-layout full-width">
  <div class="data-group cols-10">
    <table class="last-left" id="js-examination-cvi-status" style="display: <?= isset($element->cviStatus)?'':'none'; ?>">
      <colgroup>
        <col class="cols-7">
        <col class="cols-3">
      </colgroup>

      <tbody>
      <tr>
        <td>
          <input value="<?= isset($element->cviStatus)?$element->cvi_status_id:'' ?>"
                 id="OEModule_OphCiExamination_models_Element_OphCiExamination_CVI_Status_cvi_status_id"
                 name="OEModule_OphCiExamination_models_Element_OphCiExamination_CVI_Status[cvi_status_id]"
                 type="hidden">
          <span id="OEModule_OphCiExamination_models_Element_OphCiExamination_CVI_Status_text">
              <?= isset($element->cviStatus)?$element->cviStatus->name:'' ?>
          </span>
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

  <div class="add-data-actions flex-item-bottom" id="add-to-past-surgery">
    <button id="show-add-popup" class="button hint green js-add-select-search" type="button">
      <i class="oe-i plus pro-theme"></i>
    </button>
  </div>
</div>
<?php $cvi_status = PatientOphInfoCviStatus::model()->active()->findAll(array('order' => 'display_order')); ?>
<script type="text/javascript">
  $(document).ready(function () {
    var $cvi_status_list = <?= CJSON::encode(
        array_map(function ($item) {
            return ['label' =>$item->name, 'id' => $item->id];
        }, $cvi_status)
    ) ?>;
    new OpenEyes.UI.AdderDialog({
      openButton: $('#show-add-popup'),
      itemSets: [new OpenEyes.UI.AdderDialog.ItemSet($cvi_status_list)],
      onReturn: function (adderDialog, selectedItems) {
        console.log(selectedItems);
        $('#OEModule_OphCiExamination_models_Element_OphCiExamination_CVI_Status_cvi_status_id').val(selectedItems[0]['id']);
        $('#OEModule_OphCiExamination_models_Element_OphCiExamination_CVI_Status_text').text(selectedItems[0]['label']);
        $('#js-examination-cvi-status').show();
        return true;
      }
    });
  })
</script>


