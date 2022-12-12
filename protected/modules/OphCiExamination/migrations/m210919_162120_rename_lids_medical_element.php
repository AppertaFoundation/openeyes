<?php

class m210919_162120_rename_lids_medical_element extends \OEMigration
{
    private $params = [];

    public function __construct()
    {
        $examination_type_id = $this->getIdOfEventTypeByClassName('OphCiExamination');
        $this->params = [ ':event_type_id' => $examination_type_id ];
    }

    public function safeUp()
    {
        $this->execute("UPDATE element_type SET `name` = 'Lids posterior' 
                            WHERE `name` = 'Lids Medical' AND event_type_id = :event_type_id", $this->params);

        $this->execute("UPDATE index_search SET primary_term = 'Lids Posterior', secondary_term_list = 'Lids Medical' 
                        WHERE primary_term = 'Lids Medical'");
    }

    public function safeDown()
    {
        $this->execute("UPDATE element_type SET `name` = 'Lids Medical'
                            WHERE `name` = 'Lids posterior' AND event_type_id = :event_type_id", $this->params);

        $this->execute("UPDATE index_search SET primary_term = 'Lids Medical', secondary_term_list = null 
                WHERE primary_term = 'Lids Posterior'");
    }
}
