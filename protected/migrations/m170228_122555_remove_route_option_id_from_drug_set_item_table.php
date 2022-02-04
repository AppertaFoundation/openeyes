<?php

class m170228_122555_remove_route_option_id_from_drug_set_item_table extends CDbMigration
{
    public function up()
    {
        $this->dropForeignKey('drug_set_item_route_option_id_fk', 'drug_set_item');

        $this->dropColumn('drug_set_item', 'route_option_id');
        $this->dropColumn('drug_set_item_version', 'route_option_id');
    }

    public function down()
    {
        echo "m170228_122555_remove_route_option_id_from_drug_set_item_table does not support migration down.\n";
        return false;
    }
}
