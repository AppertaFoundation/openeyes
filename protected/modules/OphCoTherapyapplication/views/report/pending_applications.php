<?php $this->renderPartial('//base/_messages'); ?>
<h2>Pending Therapy Applications Report</h2>
<div class="row divider">
    <?php if ($sent) : ?>
      <span>Report sent</span>
    <?php else : ?>
      <form>
        <div class="row flex-layout flex-right">
          <button type="submit" name="report" value="generate" class="button green hint"
              <?php echo !Yii::app()->getAuthManager()->checkAccess('Report',
                  Yii::app()->user->id) ? 'disabled' : ''; ?>
          > Generate
          </button> &nbsp; &nbsp;
          <i class="spinner loader" style="display: none;"></i>
        </div>
      </form>
    <?php endif; ?>
</div>

