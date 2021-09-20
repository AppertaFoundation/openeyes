<?php

class m210915_063408_add_table_best_interest_decision_attachments extends OEMigration
{
    private const TABLE_NAME = "ophtrconsent_best_interest_decision_attachment";

    public function up()
    {
        $this->createOETable(self::TABLE_NAME, [
            "id" => "pk",
            "element_id" => "INT(11) NOT NULL",
            "protected_file_id" => "INT(10) UNSIGNED NOT NULL"
        ], true);
        $this->addForeignKey(
        "fk_ophtrconsent_bid_attachment_eid",
        self::TABLE_NAME,
        "element_id",
        "et_ophtrconsent_best_interest_decision",
        "id"
        );
        $this->addForeignKey(
            "fk_ophtrconsent_bid_attachment_pfid",
            self::TABLE_NAME,
            "protected_file_id",
            "protected_file",
            "id"
        );
    }

    public function down()
    {
        $this->dropForeignKey("fk_ophtrconsent_bid_attachment_eid", self::TABLE_NAME);
        $this->dropForeignKey("fk_ophtrconsent_bid_attachment_pfid", self::TABLE_NAME);
        $this->dropOETable(self::TABLE_NAME, true);
    }
}
