<?php

class m170915_014047_change_trial_table_status extends OEMigration
{
    const STATUS_OPEN_NO = 1;
    const STATUS_IN_PROGRESS_NO = 2;
    const STATUS_CLOSED_NO = 3;
    const STATUS_CANCELLED_NO = 4;
    const STATUS_OPEN = 'Open';
    const STATUS_IN_PROGRESS = 'In_Progress';
    const STATUS_CLOSED = 'Closed';
    const STATUS_CANCELLED = 'Cancelled';
    private static function trialStatusOptions(){
        return array(
            self::STATUS_OPEN_NO => self::STATUS_OPEN,
            self::STATUS_IN_PROGRESS_NO => self::STATUS_IN_PROGRESS,
            self::STATUS_CLOSED_NO => self::STATUS_CLOSED,
            self::STATUS_CANCELLED_NO => self::STATUS_CANCELLED,
        );
    }

	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
        $this->alterColumn('trial', 'status', 'varchar(20)');
        $sql = "UPDATE trial 
                  SET status = \"".self::trialStatusOptions()['1'] ."\" WHERE status = '1';";
        $this->execute($sql);
        $sql = "UPDATE trial 
                  SET status = \"".self::trialStatusOptions()['2'] ."\" WHERE status = '2';";
        $this->execute($sql);
        $sql = "UPDATE trial 
                  SET status = \"".self::trialStatusOptions()['3'] ."\" WHERE status = '3';";
        $this->execute($sql);
        $sql = "UPDATE trial 
                  SET status = \"".self::trialStatusOptions()['4'] ."\" WHERE status = '4';";
        $this->execute($sql);
	}

	public function safeDown()
	{
        $this->addColumn('trial','status2', 'int(10)');
        $sql = "UPDATE trial
                    SET status2 = 1
                    WHERE status = 'Open';";
        $this->execute($sql);
        $sql = "UPDATE trial
                    SET status2 = 2
                    WHERE status ='In_Progress';";
        $this->execute($sql);
        $sql = "UPDATE trial
                    SET status2 = 3
                    WHERE status = 'Closed';";
        $this->execute($sql);
        $sql = "UPDATE trial
                    SET status2 = 4
                    WHERE status = 'Cancelled';";
        $this->execute($sql);
        $this->dropColumn('trial','status');
        $this->renameColumn('trial','status2','status');

    }
}
