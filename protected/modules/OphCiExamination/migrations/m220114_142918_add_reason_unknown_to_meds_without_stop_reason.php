<?php

class m220114_142918_add_reason_unknown_to_meds_without_stop_reason extends OEMigration
{

    private function getReasonUnknownId()
    {
        return $this->dbConnection->createCommand()
        ->select('id')
        ->from('ophciexamination_medication_stop_reason')
        ->where('name = :name', array(':name' => 'Reason Unknown'))
        ->queryScalar();
    }

    private function getNextHighestDisplayOrder()
    {
        $display_order = $this->dbConnection->createCommand()
            ->select('MAX(display_order)')
            ->from('ophciexamination_medication_stop_reason')
            ->queryScalar();

        return $display_order + 1;
    }

	public function safeUp()
	{
	    $reason_unknown_id = $this->getReasonUnknownId();
	    $display_order = $this->getNextHighestDisplayOrder();
	    if (empty($reason_unknown_id)) {
	        $this->insert('ophciexamination_medication_stop_reason', ['name' => 'Reason Unknown', 'active' => 1, 'display_order' => $display_order]);
	        $reason_unknown_id = $this->getReasonUnknownId();
        }

	    $this->update('event_medication_use', ['stop_reason_id' => $reason_unknown_id], 'end_date IS NOT NULL AND stop_reason_id IS NULL');
	}

	public function safeDown()
	{
	    $reason_unknown_id = $this->getReasonUnknownId();
        $this->update('event_medication_use', ['stop_reason_id' => null], "stop_reason_id ={$reason_unknown_id}");
	}

}
