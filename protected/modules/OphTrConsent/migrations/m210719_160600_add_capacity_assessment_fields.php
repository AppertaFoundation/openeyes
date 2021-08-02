<?php

class m210719_160600_add_capacity_assessment_fields extends OEMigration
{
    private $table = "et_ophtrconsent_capacity_assessment";
    private $table_v = "et_ophtrconsent_capacity_assessment_version";

    private $clr_table = "ophtrconsent_lack_of_capacity_reason";
    private $pivot_table = "et_ophtrconsent_capacity_assessment_lack_cap_reason";

    public function up()
    {
        $this->createOETable($this->clr_table, array(
            "id" => "pk",
            "label" => "VARCHAR(128)"
        ), true);

        $this->insert($this->clr_table, array(
            "label" => "The patient is unable to understand information relevant to the decision"
        ));

        $this->insert($this->clr_table, array(
            "label" => "The patient is unable to retain information material to the decision"
        ));

        $this->insert($this->clr_table, array(
            "label" => "They are unable to use and or weigh this information in the decision-making process"
        ));

        $this->insert($this->clr_table, array(
            "label" => "They are unconscious"
        ));

        $this->createOETable($this->pivot_table, array(
            "id" => "pk",
            "element_id" => "INT(11) NOT NULL",
            "lack_of_capacity_reason_id" => "INT(11) NOT NULL"
        ), true);

        $this->addForeignKey("fk_et_ophtrconsent_calcar_etid", $this->pivot_table, "element_id", $this->table, "id");
        $this->addForeignKey("fk_et_ophtrconsent_calcar_rid", $this->pivot_table, "lack_of_capacity_reason_id", $this->clr_table, "id");

        $this->addColumn($this->table, "evidence", "TEXT NULL");
        $this->addColumn($this->table_v, "evidence", "TEXT NULL");

        $this->addColumn($this->table, "attempts_to_assist", "TEXT NULL");
        $this->addColumn($this->table_v, "attempts_to_assist", "TEXT NULL");

        $this->addColumn($this->table, "basis_of_decision", "TEXT NULL");
        $this->addColumn($this->table_v, "basis_of_decision", "TEXT NULL");
    }

    public function down()
    {
        $this->dropColumn($this->table, "evidence");
        $this->dropColumn($this->table_v, "evidence");

        $this->dropColumn($this->table, "attempts_to_assist");
        $this->dropColumn($this->table_v, "attempts_to_assist");

        $this->dropColumn($this->table, "basis_of_decision");
        $this->dropColumn($this->table_v, "basis_of_decision");

        $this->dropForeignKey("fk_et_ophtrconsent_calcar_etid", $this->pivot_table);
        $this->dropForeignKey("fk_et_ophtrconsent_calcar_rid", $this->pivot_table);

        $this->dropOETable($this->pivot_table, true);

        $this->dropOETable($this->clr_table, true);
    }
}
