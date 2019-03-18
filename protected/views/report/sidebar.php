<div class="js-groups">
  <span class="js-expand-all"><i class="oe-i plus"></i></span>
  <span class="js-collapse-all"><i class="oe-i minus"></i></span>
</div>

<div class="grouping">
  <div class="collapse-group" data-collapse="expanded">
    <div class="collapse-group-icon">
      <i class="oe-i minus"></i>
    </div>
    <h3 class="collapse-group-header">Core</h3>
    <ul class="reports-nav collapse-group-content">
        <?php foreach (
            array( 'Diagnoses' => '/report/diagnoses',) as $title => $uri) {?>
          <li <?php if (Yii::app()->getController()->action->id == preg_replace('/^\/report\//', '', $uri)) {?> class="selected"<?php }?>>
              <?php if (Yii::app()->getController()->action->id == preg_replace('/^\/report\//', '', $uri)) {?>
                  <?=\CHtml::link($title, array($uri), array('class' => 'selected'))?>
              <?php } else {?>
                  <?=\CHtml::link($title, array($uri))?>
              <?php }?>
          </li>
        <?php }?>
    </ul>
  </div>
    <?php foreach (ModuleReports::getAll() as $module => $items) {?>
      <div class="collapse-group" data-collapse="expanded">
        <div class="collapse-group-icon">
          <i class="oe-i minus"></i>
        </div>
        <h3 class="collapse-group-header"><?php echo $module?></h3>
        <ul class="reports-nav collapse-group-content">
            <?php foreach ($items as $item => $uri) {
                $e = explode('/', $uri);
                $action = array_pop($e)?>
              <li<?php if (Yii::app()->getController()->action->id == $action) {?> class="selected"<?php }?>>
                  <?php if (Yii::app()->getController()->action->id == $action) {?>
                      <?=\CHtml::link($item, array($uri), array('class' => 'selected'))?>
                  <?php } else {?>
                      <?=\CHtml::link($item, array($uri))?>
                  <?php }?>
              </li>
            <?php }?>
        </ul>
      </div>
    <?php }?>
</div>

