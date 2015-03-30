<?php
/**
 * Created by PhpStorm.
 * User: petergallagher
 * Date: 30/03/15
 * Time: 15:00
 */

?>

<?php $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
	'id'=>'generic-search-form',
	'enableAjaxValidation'=>false,
	'layoutColumns' => array(
		'label' => 10,
		'field' => 5
	)
));?>
	<?php foreach($search->getSearchItems() as $key => $value):
		$name = 'search[' . $key . ']';
		if(is_array($value)):
			$comparePlaceholder = 'Search for ' . $search->getModel()->getAttributeLabel($key);
			foreach($value as $searchKey => $searchValue):
				if($searchKey === 'compare_to'):
					foreach($searchValue as $compareTo):
						$comparePlaceholder .= ', ' . $search->getModel()->getAttributeLabel($compareTo);
						echo CHtml::hiddenField('search[' . $key . '][compare_to][]', $compareTo);
					endforeach;
				endif;
			endforeach;
			echo CHtml::textField($name, $search->getSearchTermForAttribute($key), array(
				'autocomplete'=>Yii::app()->params['html_autocomplete'],
				'placeholder' => $comparePlaceholder
			));
		else:
			echo CHtml::label($search->getModel()->getAttributeLabel($key), Chtml::getIdByName($name));
			echo CHtml::textField($name, $search->getSearchTermForAttribute($key), array('autocomplete'=>Yii::app()->params['html_autocomplete']));
		endif;
	endforeach;
	?>
	<?php echo $form->formActions(array('submit' => 'search', 'cancel' => false));?>
<?php $this->endWidget()?>