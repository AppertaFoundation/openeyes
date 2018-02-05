<?php
$this->beginWidget('CActiveForm', array(
    'id' => 'search-form',
    'focus' => '#query',
    'action' => Yii::app()->createUrl('site/search'),
    'htmlOptions' => array(
        'class' => 'form oe-find-patient search',
    ),
)); ?>
<div class="oe-search-patient" id="oe-search-patient">
    <div class="search-patient">
        <?php echo CHtml::textField('query', '', array('autocomplete' => Yii::app()->params['html_autocomplete'], 'class' => 'search', 'placeholder' => 'Search')); ?>
        <button type="submit" id="js-find-patient" class="blue hint">Find Patient</button>
        <div class="find-by">Search by Hospital Number, NHS Number, Firstname Surname or Surname, Firstname</div>
        <div class="text-center">
            <img class="loader" src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif') ?>"
                 alt="loading..."/>
        </div>
    </div>
</div>
<?php $this->endWidget(); ?>
