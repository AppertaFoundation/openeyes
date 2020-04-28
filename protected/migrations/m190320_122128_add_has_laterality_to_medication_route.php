<?php

class m190320_122128_add_has_laterality_to_medication_route extends CDbMigration
{
    public function up()
    {
        $this->addColumn("medication_route", "has_laterality", "BOOLEAN NOT NULL DEFAULT 0");
        $this->addColumn("medication_route_version", "has_laterality", "BOOLEAN NOT NULL DEFAULT 0");
        $this->execute("UPDATE medication_route SET has_laterality =1 WHERE `term` IN ('Eye', 'Intravitreal', 'Ocular')");
    }

    public function down()
    {
        $this->dropColumn("medication_route", "has_laterality");
        $this->dropColumn("medication_route_version", "has_laterality");
    }
}
