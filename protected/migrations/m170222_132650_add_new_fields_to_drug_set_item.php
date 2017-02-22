<?php

class m170222_132650_add_new_fields_to_drug_set_item extends CDbMigration
{
    public function up()
	{
	    $this->addColumn('route_id', 'drug_set_item', 'INT(10) UNSIGNED');
	    $this->addColumn('route_option_id', 'drug_set_item', 'INT(10) UNSIGNED');

	    $this->addColumn('route_id', 'drug_set_item_version', 'INT(10) UNSIGNED');
	    $this->addColumn('route_option_id', 'drug_set_item_version', 'INT(10) UNSIGNED');

	    
	    $this->addForeignKey('drug_set_item_route_option_id_fk', 'drug_set_item', 'route_id', 'drug_route', 'id');
	    $this->addForeignKey('drug_set_item_route_id_fk', 'drug_set_item', 'route_option_id', 'drug_route_option', 'id');
	}

	public function down()
	{
	    $this->dropColumn('route_id', 'drug_set_item');
	    $this->dropColumn('route_option_id', 'drug_set_item');

	    $this->dropColumn('route_id', 'drug_set_item_version');
	    $this->dropColumn('route_option_id', 'drug_set_item_version');
	    
	    $this->dropForeignKey('drug_set_item_route_option_id_fk', 'drug_set_item');
	    $this->dropForeignKey('drug_set_item_route_id_fk', 'drug_set_item');
	}
}