<?php

class m210909_074525_add_esign_element_to_cf_layouts extends CDbMigration
{
    public function safeUp()
    {
        $et_id = $this->getElementTypeId();
        $this->execute("INSERT INTO ophtrconsent_type_assessment (element_id, type_id, display_order)
                        VALUES ($et_id, 1, 0), ($et_id, 2, 0), ($et_id, 3, 0), ($et_id, 4, 0)
                        ");
    }

    public function down()
    {
        $this->execute("DELETE FROM ophtrconsent_type_assessment WHERE element_id = ".$this->getElementTypeId());
    }

    private function getElementTypeId()
    {
        return $this->dbConnection->createCommand("SELECT id FROM element_type WHERE class_name = :cn")
            ->bindValue(":cn", "Element_OphTrConsent_Esign")->queryScalar();
    }
}
