<?php

class m210826_105900_add_default_templates extends CDbMigration
{
    public function safeUp()
    {
        $procedures = $this->dbConnection->createCommand("SELECT id, term, snomed_code
                                                        FROM proc
                                                        WHERE term
                                                        IN ('Course of LUCENTIS anti-VEGF Intravitreal Treatment by either nurse practitioner or doctor',
                                                        'Course of EYLEA anti-VEGF Intravitreal Treatment by either nurse practitioner or doctor',
                                                        'Course of BEOVU anti-VEGF Intravitreal Treatment by either nurse practitioner or doctor',
                                                        'Course of AVASTIN anti-VEGF Intravitreal Treatment by either nurse practitioner or doctor')")->queryAll();

        $consent_3 = $this->dbConnection->createCommand('SELECT id, name
                                                        FROM ophtrconsent_type_type
                                                        WHERE name = "3. Patient/parental agreement to investigation or treatment (procedures where consciousness not impaired)"')->queryRow();

        foreach($procedures as $procedure) {
            $this->insert('ophtrconsent_template', array('name' => $procedure['term'], 'type_id' => $consent_3['id']));
            $insert_id = Yii::app()->db->getLastInsertID();
            $this->insert('ophtrconsent_template_procedure', array('procedure_id' => $procedure['id'], 'template_id' => $insert_id));
        }
    }

    public function safeDown()
    {
        echo "m210826_105900_add_default_templates does not support migration down.\n";

        return false;
    }
}
