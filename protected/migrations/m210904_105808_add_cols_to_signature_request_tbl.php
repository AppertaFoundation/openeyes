<?php

class m210904_105808_add_cols_to_signature_request_tbl extends OEMigration
{
    private $table = "signature_request";

    public function up()
    {
        $this->addColumn($this->table, 'initiator_element_type_id', 'INT(10) UNSIGNED NULL DEFAULT NULL');
        $this->addColumn($this->table, 'initiator_row_id', 'INT(10) UNSIGNED NULL DEFAULT NULL');
    }

    public function down()
    {
        $this->dropColumn($this->table, 'initiator_element_type_id');
        $this->dropColumn($this->table, 'initiator_row_id');
    }
}
