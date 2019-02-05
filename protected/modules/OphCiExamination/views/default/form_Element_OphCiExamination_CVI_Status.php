
<?php
  list($latest_cvi_status, $last_cvi_date) = $this->patient->getCviSummary();
  $latest_cvi_status_id = PatientOphInfoCviStatus::model()->findByAttributes(array('name' =>$latest_cvi_status))->id;
  if (!is_string($last_cvi_date)) {
    $last_cvi_date = null;
  }
  else {
    $last_cvi_date = Helper::formatFuzzyDate($last_cvi_date);
  }
?>
<div class="element-fields flex-layout full-width">
  <div class="data-group cols-10">
    <table class="last-left" id="js-examination-cvi-status">
      <colgroup>
        <col class="cols-7">
        <col class="cols-3">
      </colgroup>

      <tbody>
      <tr>
        <td>
          <input value="<?= isset($element->cviStatus)?$element->cvi_status_id: $latest_cvi_status_id ?>"
                 id="<?= CHtml::modelName($element).'_cvi_status_id'?>"
                 name="<?= CHtml::modelName($element).'[cvi_status_id]' ?>"
                 type="hidden">
          <span id="<?= CHtml::modelName($element).'_text'?>">
              <?= isset($element->cviStatus)?$element->cviStatus->name:$latest_cvi_status ?>
          </span>
        </td>
        <td>
          <input id= "<?= CHtml::modelName($element) . '_element_date_0'?>"
                 placeholder="dd Mmm yyyy"
                 name="<?= CHtml::modelName($element) . '[element_date]' ?>"
                 value="<?= isset($element->element_date) ? $element->getFormatedDate() : $last_cvi_date ?>"
                 autocomplete="off"/>
        </td>
      </tr>
      </tbody>
    </table>
  </div>

  <div class="add-data-actions flex-item-bottom" id="add-to-cvi-status">
    <button id="show-add-cvi-popup" class="button hint green js-add-select-search" type="button">
      <i class="oe-i plus pro-theme"></i>
    </button>
  </div>
</div>
<?php $cvi_status = PatientOphInfoCviStatus::model()->active()->findAll(array('order' => 'display_order')); ?>
<script type="text/javascript">
  $(document).ready(function () {
    pickmeup("#<?= CHtml::modelName($element) . '_element_date_0' ?>", {
      format: 'd b Y',
      hide_on_select: true,
      default_date: false,
      max: new Date(),
    });

    var $cvi_status_list = <?= CJSON::encode(
        array_map(function ($item) {
            return ['label' =>$item->name, 'id' => $item->id];
        }, $cvi_status)
    ) ?>;
    new OpenEyes.UI.AdderDialog({
      openButton: $('#show-add-cvi-popup'),
      itemSets: [new OpenEyes.UI.AdderDialog.ItemSet($cvi_status_list)],
      onReturn: function (adderDialog, selectedItems) {
        if (selectedItems.length){
          $("#<?= CHtml::modelName($element).'_cvi_status_id'?>").val(selectedItems[0]['id']);
          $("#<?= CHtml::modelName($element).'_text'?>").text(selectedItems[0]['label']);
        }
        return true;
      }
    });
  })
</script>


