<?php

class m180613_113201_add_comments_field_to_presc_item extends OEMigration
{
    public function up()
    {
        $this->addColumn('ophdrprescription_item', 'comments', 'TINYTEXT');
        $this->addColumn('ophdrprescription_item_version', 'comments', 'TINYTEXT');
    }

    public function down()
    {
        $this->dropColumn('ophdrprescription_item', 'comments');
        $this->dropColumn('ophdrprescription_item_version', 'comments');
    }
}
