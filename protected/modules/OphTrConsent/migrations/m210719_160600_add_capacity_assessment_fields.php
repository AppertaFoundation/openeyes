<?php

class m210719_160600_add_capacity_assessment_fields extends OEMigration
{
    private $table = "et_ophtrconsent_capacity_assessment";
    private $table_v = "et_ophtrconsent_capacity_assessment_version";

    private $clr_table = "ophtrconsent_lack_of_capacity_reason";
    private $pivot_table = "et_ophtrconsent_capacity_assessment_lack_cap_reason";

    public function up()
    {
        if ($this->dbConnection->schema->getTable($this->clr_table, true) === null) {
            $this->createOETable($this->clr_table, array(
                "id" => "pk",
                "label" => "VARCHAR(128)"
            ), true);

            $this->insertMultiple(
                $this->clr_table,
                [
                    ['label' => 'The patient is unable to understand information relevant to the decision'],
                    ['label' => 'The patient is unable to retain information material to the decision'],
                    ['label' => 'They are unable to use and or weigh this information in the decision-making process'],
                    ['label' => 'They are unconscious'],
                ]
            );
        } else {
            $query_advocate = $this->dbConnection->createCommand('SELECT * FROM ophtrconsent_lack_of_capacity_reason WHERE label="They are unconscious"')->query();
            if($query_advocate->rowCount == 0) {
                $this->insertMultiple(
                    $this->clr_table,
                    [
                        ['label' => 'They are unable to use and or weigh this information in the decision-making process'],
                        ['label' => 'They are unconscious'],
                    ]
                );
            }

            $old_unable_id = $this->dbConnection->createCommand('SELECT id FROM ' . $this->clr_table . ' WHERE `label` = "The patient is unable to use and weigh this information in the decision-making process"')->queryScalar();
            $old_unconscious_id = $this->dbConnection->createCommand('SELECT id FROM ' . $this->clr_table . ' WHERE `label` = "The patient is unable to communicate their decision"')->queryScalar();


            $unable_id = $this->dbConnection->createCommand('SELECT id FROM ' . $this->clr_table . ' WHERE `label` = "They are unable to use and or weigh this information in the decision-making process"')->queryScalar();
            $unconscious_id = $this->dbConnection->createCommand('SELECT id FROM ' . $this->clr_table . ' WHERE `label` = "They are unconscious"')->queryScalar();

            $this->execute("
                UPDATE " . $this->clr_table . 
                " SET lack_of_capacity_reason_id = CASE
                    WHEN lack_of_capacity_reason_id = " . $old_unable_id . " THEN " . $unable_id . "
                    WHEN lack_of_capacity_reason_id = " . $old_unconscious_id . " THEN " . $unconscious_id . "
                    ELSE lack_of_capacity_reason_id
                END
                WHERE lack_of_capacity_reason_id  in (" . $old_unable_id . "," . $old_unconscious_id . ")
            ");
            /*$this->dropForeignKey("fk_et_ophtrconsent_calcar_etid", $this->pivot_table);
            $this->dropForeignKey("fk_et_ophtrconsent_calcar_rid", $this->pivot_table);
            $this->truncateTable($this->clr_table);
            if ($this->dbConnection->schema->getTable($this->pivot_table, true) !== null) {
                $this->truncateTable($this->pivot_table);
                $this->addForeignKey("fk_et_ophtrconsent_calcar_rid", $this->pivot_table, "lack_of_capacity_reason_id", $this->clr_table, "id");
            }
            $this->addForeignKey("fk_et_ophtrconsent_calcar_etid", $this->pivot_table, "element_id", $this->table, "id");*/
        }

        if ($this->dbConnection->schema->getTable($this->pivot_table, true) === null) {
            $this->createOETable($this->pivot_table, array(
                "id" => "pk",
                "element_id" => "INT(11) NOT NULL",
                "lack_of_capacity_reason_id" => "INT(11) NOT NULL"
            ), true);

            $this->addForeignKey("fk_et_ophtrconsent_calcar_etid", $this->pivot_table, "element_id", $this->table, "id");
            $this->addForeignKey("fk_et_ophtrconsent_calcar_rid", $this->pivot_table, "lack_of_capacity_reason_id", $this->clr_table, "id");
        }
    }

    public function down()
    {
        echo "m210719_160600_add_capacity_assessment_fields does not support migration down.\n";
        return false;
    }
}
