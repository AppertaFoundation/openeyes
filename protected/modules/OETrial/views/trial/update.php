<?php
/* @var $this TrialController */
/* @var $model Trial */

?>
<h1 class="badge">Trial</h1>
<div class="box content admin-content">
  <div class="large-10 column content admin large-centered">
    <div class="box admin">
        <?php
        $this->widget('zii.widgets.CBreadcrumbs', array(
            'links' => $this->breadcrumbs,
        ));
        ?>

      <h1 class="text-center">Update Trial Details</h1>
        <?php $this->renderPartial('_form', array('model' => $model)); ?>
    </div>
  </div>
</div>

