<?php

class m170222_195519_add_issue_to_draft_prescriptions extends CDbMigration
{
        // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        $draft_id = $this->dbConnection->createCommand()->select('*')->from('issue')->where('name = :name', array(':name' => 'Draft'))->queryScalar();

        //no Draft in the issue table, we have to add it
        if(!$draft_id){
            $this->insert('issue', array('name' => 'Draft') );
        }

        $this->execute("INSERT INTO event_issue (event_id, issue_id, last_modified_date, created_date, created_user_id)
                            SELECT pd.event_id, (SELECT id FROM issue WHERE name = 'Draft'), pd.last_modified_date, pd.created_date, pd.created_user_id
                            FROM   et_ophdrprescription_details pd
                            WHERE  pd.draft = 1
                            AND  pd.event_id 
                              NOT IN (SELECT event_id FROM event_issue WHERE issue_id = (SELECT id FROM issue WHERE name = 'Draft'));");
    }

    public function safeDown()
    {
            echo "m170222_195519_add_issue_to_draft_prescriptions does not support migration down.\n";
            return false;
    }

}