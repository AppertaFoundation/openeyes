<?php

class m161026_135722_remove_patient_merge_unique_audit_action extends CDbMigration
{
    public function up()
    {
            /* check if the action is already in the DB */
            $audit_action_id = $this->dbConnection->createCommand('SELECT id FROM audit_action WHERE name = "Patient Merge Request successfully done."')->queryScalar();

            /* get all actions we want to delete from audit_action */
            $audit_actions = $this->dbConnection->createCommand('SELECT id FROM audit_action WHERE name LIKE "%(hos_num) successfully done.%"')->queryAll();

            /* check if there is anything to remove, if not we just return */
        if (!$audit_actions) {
            return true;
        }

            /* Insert the action if there is not in the DB */
        if (!$audit_action_id) {
            $this->insert('audit_action', array('name' => 'Patient Merge Request successfully done.'));
        }

            //get the action id we want to us
            $audit_action_id = $this->dbConnection->createCommand('SELECT id FROM audit_action WHERE name = "Patient Merge Request successfully done."')->queryScalar();

            $action_ids = array();
        if ($audit_actions) {
            foreach ($audit_actions as $action) {
                $action_ids[] = $action['id'];
            }
        }

             /* update the audit table to set the new action id and remove the reference to unwanted actions */
            $this->update('audit', array('action_id' => $audit_action_id), 'action_id IN (' . (implode(',', $action_ids)) . ')');
            $this->delete('audit_action', 'id IN (' . (implode(',', $action_ids)) . ')');
    }

    public function down()
    {
    }
}
