<?php

class m170222_132650_add_new_fields_to_drug_set_item extends CDbMigration
{
    public function up()
    {
        $this->addColumn('drug_set_item', 'route_id', 'INT(10) UNSIGNED');
        $this->addColumn('drug_set_item', 'route_option_id', 'INT(10) UNSIGNED');

        $this->addColumn('drug_set_item_version', 'route_id', 'INT(10) UNSIGNED');
        $this->addColumn('drug_set_item_version', 'route_option_id', 'INT(10) UNSIGNED');


        $this->addForeignKey('drug_set_item_route_id_fk', 'drug_set_item', 'route_id', 'drug_route', 'id');
        $this->addForeignKey('drug_set_item_route_option_id_fk', 'drug_set_item', 'route_option_id', 'drug_route_option', 'id');
    }

    public function down()
    {
        $this->dropForeignKey('drug_set_item_route_option_id_fk', 'drug_set_item');
        $this->dropForeignKey('drug_set_item_route_id_fk', 'drug_set_item');

        $this->dropColumn('drug_set_item', 'route_id');
        $this->dropColumn('drug_set_item', 'route_option_id');

        $this->dropColumn('drug_set_item_version', 'route_id');
        $this->dropColumn('drug_set_item_version', 'route_option_id');
    }
}
