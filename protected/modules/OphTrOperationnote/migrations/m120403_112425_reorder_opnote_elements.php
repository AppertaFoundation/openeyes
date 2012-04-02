<?php

class m120403_112425_reorder_opnote_elements extends CDbMigration
{
	public function up()
	{
		$this->update('element_type',array('display_order'=>2),"event_type_id = 4 and class_name in ('ElementMembranePeel','ElementTamponade','ElementBuckle','ElementCataract')");
		$this->update('element_type',array('display_order'=>3),"event_type_id = 4 and class_name in ('ElementAnaesthetic','ElementSurgeon')");
	}

	public function down()
	{
		$this->update('element_type',array('display_order'=>3),"event_type_id = 4 and class_name = 'ElementMembranePeel'");
		$this->update('element_type',array('display_order'=>4),"event_type_id = 4 and class_name = 'ElementTamponade'");
		$this->update('element_type',array('display_order'=>5),"event_type_id = 4 and class_name = 'ElementBuckle'");
		$this->update('element_type',array('display_order'=>6),"event_type_id = 4 and class_name = 'ElementCataract'");
		$this->update('element_type',array('display_order'=>7),"event_type_id = 4 and class_name = 'ElementAnaesthetic'");
		$this->update('element_type',array('display_order'=>8),"event_type_id = 4 and class_name = 'ElementSurgeon'");
	}
}
