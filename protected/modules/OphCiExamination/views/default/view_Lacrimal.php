<div class="element-data element-eyes flex-layout">
    <?php foreach (array('left' => 'right', 'right' => 'left') as $page_side => $eye_side) : ?>
      <div class="js-element-eye cols-6 <?= $eye_side; ?>-eye">
        <div class="data-group">
            <?php if ($element->hasEye($eye_side)) : ?>
              <div class="eyedraw flex-layout flex-top anterior-segment">
                  <?php $this->renderPartial(
                      $element->view_view . '_OEEyeDraw',
                      array('side' => $eye_side, 'element' => $element)
                  ); ?>
              </div>
            <?php else : ?>
              <div class="data-value not-recorded">Not recorded</div>
            <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
</div>
