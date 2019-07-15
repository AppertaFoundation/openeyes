<?php

use OEModule\OphCiExamination\models\OphCiExamination_Attribute;

class AttributeOptionsAdminController extends BaseAdminController
{
	public $itemsPerPage = 100;

	public $group = 'Operation note';

	public function actionIndex($attribute_id)
	{
		$this->genericAdmin(
			'Manage Attribute Options',
			OphTrOperationnote_AttributeOption::class,
			array(
				'label_field' => 'value',

				'filter_fields' => array(
					array('field' => 'attribute_id', 'model' => OphTrOperationnote_Attribute::class),
				),
				/*'extra_fields' => array(
					array('field' => 'subspecialty_id', 'type' => 'lookup', 'model' => 'Subspecialty'),
				),*/
				'div_wrapper_class' => 'cols-5',
				'return_url' => '/OphTrOperationnote/attributesAdmin/list',
			)
		);
	}
}