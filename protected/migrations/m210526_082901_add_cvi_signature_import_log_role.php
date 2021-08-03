<?php

class m210526_082901_add_cvi_signature_import_log_role extends CDbMigration
{
    private $name = "Signature Import Log";

    public function up()
    {
        $this->insert("authitem", array(
            "name" => $this->name,
            "type" => 2,
            "description" => "Allows non-admin users to signature import logs"
        ));
    }

    public function down()
    {
        $this->execute("DELETE FROM authitem WHERE `name` = '{$this->name}'");
    }
}
