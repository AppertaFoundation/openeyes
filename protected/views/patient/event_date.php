<?php
if (!Yii::app()->request->isPostRequest) {
    if ($this->event->event_date) {
        if (preg_match('/^[0-9]+ [a-zA-Z]+ [0-9]+$/', $this->event->event_date)) {
            $value = $this->event->event_date;
        } else {
            $value = date('j M Y', strtotime($this->event->event_date));
        }
    } else {
      $value = date('j M Y');
    }
} else {
  $value = $this->event->event_date;
}
?>
<section class="element edit full edit-date">
  <header class="element-header">
    <h3 class="element-title"><?php echo CHtml::encode($this->event->getAttributeLabel('event_date')) ?></h3>
  </header>
  <div class="element-fields full-width">
    <div>
        <?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
            'name' => CHtml::modelName($this->event) . '[event_date]',
            'id' => CHtml::modelName($this->event) . '_event_date_0',
            // additional javascript options for the date picker plugin
            'options' => array(
                'maxDate' => 'today',
                'showAnim' => 'fold',
                'dateFormat' => Helper::NHS_DATE_FORMAT_JS,
            ),
            'value' => (preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $value) ? Helper::convertMySQL2NHS($value) : $value),
            'htmlOptions' => array(
                'style' => 'margin-left:8px',
            ),
        )); ?>
    </div>
  </div>
</section>