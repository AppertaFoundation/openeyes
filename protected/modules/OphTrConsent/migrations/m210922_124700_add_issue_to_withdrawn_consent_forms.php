<?php

class m210922_124700_add_issue_to_withdrawn_consent_forms extends OEMigration
{
    public function safeUp()
    {
        $withdrawn_id = $this->dbConnection->createCommand()->select('*')->from('issue')->where('name = :name', array(':name' => 'Consent Withdrawn'))->queryScalar();

        //no Consent Withdrawn in the issue table, we have to add it
        if (!$withdrawn_id) {
            $this->insert('issue', array('name' => 'Consent Withdrawn'));
        }

        $this->execute("INSERT INTO event_issue (event_id, issue_id, last_modified_date, created_date, created_user_id)
                            SELECT cw.event_id, (SELECT id FROM issue WHERE name = 'Consent Withdrawn'), cw.last_modified_date, cw.created_date, cw.created_user_id
                            FROM   et_ophtrconsent_withdrawal cw
                            WHERE  cw.signature_id IS NOT NULL
                            AND  cw.event_id
                              NOT IN (SELECT event_id FROM event_issue WHERE issue_id = (SELECT id FROM issue WHERE name = 'Consent Withdrawn'));");
    }

    public function safeDown()
    {
            echo "m210922_124700_add_issue_to_withdrawn_consent_forms does not support migration down.\n";
            return true;
    }
}
