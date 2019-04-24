<?php
/**
 * @var $callback string
 */
$this->beginWidget('CActiveForm', array(
    'id' => 'search-form',
    'focus' => '#query',
    'action' => $callback,
    'htmlOptions' => array(
        'class' => 'form oe-find-patient search',
    ),
)); ?>

<div class="oe-search-patient" id="oe-search-patient">
    <div class="search-patient">
        <?=\CHtml::textField('query', isset($search_term)?$search_term:'', array('autocomplete' => 'off', 'class' => 'search', 'placeholder' => 'Search')); ?>
        <button type="submit" id="js-find-patient" class="blue hint">Find Patient</button>
        <div class="find-by">Search by Hospital Number, <?php echo Yii::app()->params['nhs_num_label']?> Number, Firstname Surname or Surname, Firstname</div>
      <i class="spinner" style="display: none;" title="Loading..."></i>
    </div>
</div>
<?php $this->endWidget(); ?>
