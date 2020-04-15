<?php

class m180531_133603_add_is_active_flag_to_document_sub_types extends CDbMigration
{
    public function up()
    {
        $this->addColumn('ophcodocument_sub_types', 'is_active', 'TINYINT(1) DEFAULT 1 AFTER display_order');
        $this->addColumn('ophcodocument_sub_types_version', 'is_active', 'TINYINT(1) DEFAULT 1 AFTER display_order');
    }

    public function down()
    {
        $this->dropColumn('ophcodocument_sub_types', 'is_active');
        $this->dropColumn('ophcodocument_sub_types_version', 'is_active');
    }
}
