<?php

class m110325_160901_create_iop_element extends CDbMigration
{
    public function up()
    {
		$this->addColumn('element_intraocular_pressure', 'right_iop', 'tinyint');
		$this->addColumn('element_intraocular_pressure', 'left_iop', 'tinyint');

		$elementType = ElementType::model()->findByAttributes(array(
			'name' => 'Intraocular pressure',
			'class_name' => 'ElementIntraocularPressure'
		));

		$this->insert('possible_element_type', array(
			'event_type_id' => 1,
			'element_type_id' => $elementType->id,
			'num_views' => 1,
			'order' => 10
		));
    }

    public function down()
    {
		$this->dropColumn('element_intraocular_pressure', 'right_iop');
		$this->dropColumn('element_intraocular_pressure', 'left_iop');

		$elementType = ElementType::model()->findByAttributes(array(
			'name' => 'Intraocular pressure',
			'class_name' => 'ElementIntraocularPressure'
		));

		$this->delete('possible_element_type', 'element_type_id = :id',
			array(':id' => $elementType->id)
		);
    }
}