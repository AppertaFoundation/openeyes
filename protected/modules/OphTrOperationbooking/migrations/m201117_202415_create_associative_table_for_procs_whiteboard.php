<?php

class m201117_202415_create_associative_table_for_procs_whiteboard extends OEMigration
{
    public function safeUp()
    {
        // get all the whiteboard ids and procedures and push them to the $whiteboardProcAssignmentData array
        // to insert into ophtroperationbooking_whiteboard_proc_assignment table.

        $whiteboardTableRows = $this->dbConnection->createCommand('
            SELECT ow.id, ow.procedure
            FROM ophtroperationbooking_whiteboard ow
        ')->queryAll();

        $whiteboardProcAssignmentData = array_map(function ($whiteboardTableRow) {
            $whiteboardId = $whiteboardTableRow['id'];

            $procedures = array_map('trim', explode(",", $whiteboardTableRow['procedure']));

            $results = $this->dbConnection->createCommand()
                ->select('id as proc_id, row_number()over(order by id) as display_order, ' . $whiteboardId . ' as whiteboard_id')
                ->from('proc')
                ->where(['in', 'term', $procedures])
                ->queryAll();

            return $results;
        }, $whiteboardTableRows);

        // create ophtroperationbooking_whiteboard_proc_assignment table
        $this->createOETable(
            'ophtroperationbooking_whiteboard_proc_assignment',
            array(
                'id' => 'pk',
                'whiteboard_id' => 'int(11)',
                'proc_id' => 'int(10) unsigned NOT NULL',
                'display_order' => 'tinyint(3) unsigned DEFAULT 10',
            ),
            true
        );

        // Add Foreign Key
        $this->addForeignKey(
            'ophtroperationbooking_whiteboard_wid_whiteboard_fk',
            'ophtroperationbooking_whiteboard_proc_assignment',
            'whiteboard_id',
            'ophtroperationbooking_whiteboard',
            'id'
        );

        $this->addForeignKey(
            'ophtroperationbooking_whiteboard_procid_proc_fk',
            'ophtroperationbooking_whiteboard_proc_assignment',
            'proc_id',
            'proc',
            'id'
        );

        if (count($whiteboardProcAssignmentData) > 0) {
            foreach ($whiteboardProcAssignmentData as $wpad) {
                if (!empty($wpad)) {
                    $this->insertMultiple('ophtroperationbooking_whiteboard_proc_assignment', $wpad);
                }
            }
        }

        // Once the data is moved to the ophtroperationbooking_whiteboard_proc_assignment table, remove the
        // procedure column from the ophtroperationbooking_whiteboard table
        $this->dropOEColumn('ophtroperationbooking_whiteboard', 'procedure', true);
    }

    public function safeDown()
    {
        echo 'this migration does not support down';
    }
}
