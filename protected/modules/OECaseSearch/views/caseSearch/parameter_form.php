<?php
/* @var $this CaseSearchController */
/* @var $id int */
/* @var $model CaseSearchParameter */
/* @var $form CActiveForm */
?>
<tr class="parameter" id="<?php echo $id; ?>">
  <td>
    <div class="<?php echo $model->name; ?> flex-layout"
         style="padding-bottom: 6px; padding-top: 6px;"
    >
      <div class="js-case-search-AND-label ">
        <p class="highlighter"
           style='margin-right: 8px;'>AND</p>
      </div>
      <div class="cols-10">
          <?php $model->renderParameter($id); ?>
          <?php echo CHtml::activeHiddenField($model, "[$id]id"); ?>
      </div>
      <div class="cols-2">
        <i id="<?= $id ?>-remove" class="oe-i trash"></i>
      </div>
      <hr/>
    </div>
  </td>
  <script type="text/javascript">
    $(document).ready(function () {
      $('.parameter').each(function(){
        if($(this).index() == 0){
          $(this).find('.js-case-search-AND-label').remove();
        }
      });
    });
    $('#<?= $id?>-remove').on('click', function () {
      if ($(this).closest('.parameter').index() == 0){
        $('.js-case-search-AND-label').first().remove();
      }
      this.closest('.parameter').remove();
    })
  </script>
</tr>



