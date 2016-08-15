<?php

class m151015_120507_proc_subspecialty_assignment_add_ordering extends CDbMigration
{
    public function up()
    {
        // add the display order field
        $this->addColumn('proc_subspecialty_assignment', 'display_order', 'int unsigned NOT NULL AFTER subspecialty_id');
        $this->addColumn('proc_subspecialty_assignment_version', 'display_order', 'int unsigned NOT NULL AFTER subspecialty_id');

        // loop through all records, ordered by subspecialty and name, and add an incrementing display_order value (starting from 2)
        $seq = 2;

        foreach ($this->getDbConnection()->createCommand('SELECT psa.id from proc_subspecialty_assignment psa, proc WHERE psa.proc_id=proc.id ORDER BY subspecialty_id,term')->queryAll() as $row) {
            $this->update('proc_subspecialty_assignment', array('display_order' => $seq), "id = {$row['id']} AND subspecialty_id=4");
            ++$seq;
        }

        //  Fetch the id of the proc record that matches 'Phokomulsification and IOL'
        if ($row = $this->getDbConnection()->createCommand()->select('id')->from('proc')->where('term="Phakoemulsification and IOL"')->queryRow()) {
            $recId = $row['id'];

            // modify the entry 'Phokomulsification and IOL' to be display_order 1.
            $this->update('proc_subspecialty_assignment', array('display_order' => 1), "proc_id = $recId");
        }
    }

    public function down()
    {
        // remove the display_order field
        $this->dropColumn('proc_subspecialty_assignment', 'display_order');
        $this->dropColumn('proc_subspecialty_assignment_version', 'display_order');
    }
}
