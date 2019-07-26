<script type="text/javascript" src="<?= Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.widgets.js') . '/AutoCompleteSearch.js', false, -1); ?>"></script>
<?= \CHtml::activeTextField($set, 'name', [
    'class' => 'cols-full js-auto autocomplete search',
    'style' => $style,
    'placeholder' => 'Search auto set',
    'id' => 'MedicationSet_auto_name'
]); ?>

