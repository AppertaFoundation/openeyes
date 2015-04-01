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
	'method' => 'get'
));?>
	<div>
	<?php foreach($search->getSearchItems() as $key => $value):
		$name = 'search[' . $key . ']';
		if(is_array($value)):
			$type = isset($value['type']) ? $value['type'] : 'compare';
			switch ($type){
				case 'compare':
					$comparePlaceholder = $search->getModel()->getAttributeLabel($key);
					foreach($value as $searchKey => $searchValue):
						if($searchKey === 'compare_to'):
							foreach($searchValue as $compareTo):
								$comparePlaceholder .= ', ' . $search->getModel()->getAttributeLabel($compareTo);
								echo CHtml::hiddenField('search[' . $key . '][compare_to]['.$compareTo.']', $compareTo);
							endforeach;
						endif;
					endforeach;?>
					<div class="single-search-field">
						<?php
						$name .= '[value]';
						echo CHtml::textField($name, $search->getSearchTermForAttribute($key), array(
							'autocomplete'=>Yii::app()->params['html_autocomplete'],
							'placeholder' => $comparePlaceholder
						));?>
					</div>
					<?php break;
				case 'boolean':
						?>
						<div>
						<?php
						echo CHtml::dropDownList($name, $search->getSearchTermForAttribute($key), array(
							'' => 'All',
							'1' => 'Only ' . $search->getModel()->getAttributeLabel($key),
							'0' => 'Exclude ' . $search->getModel()->getAttributeLabel($key)
						));
						?>
						</div>
						<?php
					break;

			}
		else: ?>
			<div>
			<?php
			echo CHtml::textField($name, $search->getSearchTermForAttribute($key), array(
				'autocomplete'=>Yii::app()->params['html_autocomplete'],
				'placeholder' => $search->getModel()->getAttributeLabel($key)
			));
			?>
			</div>
		<?php
		endif;
	endforeach;
	?>
		<div class="submit-row">
			<button class="button small primary event-action" name="save" type="submit">Search</button>
		</div>
	</div>

<?php $this->endWidget()?>