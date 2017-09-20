<?php

class m170913_135250_confirm_v2_disorders extends CDbMigration
{
    protected $disorders = array(
        array('95725002', 'Corneal Laceration (disorder)', 'Corneal Laceration', 130),
        array('77676001', 'Prolapse of Iris (disorder)', 'Prolapse of Iris', 130),
    );

	public function up()
	{
	    foreach ($this->disorders as $disorder_spec) {
            if (!$this->getDbConnection()->createCommand()
                ->select('count(*)')
                ->from('disorder')
                ->where('id = ?')
                ->queryScalar(array($disorder_spec[0])) ) {

                $this->insert('disorder', array(
                    'id' => $disorder_spec[0],
                    'fully_specified_name' => $disorder_spec[1],
                    'term' => $disorder_spec[2],
                    'specialty_id' => $disorder_spec[3]
                ));
            }
        }
	}

	public function down()
	{
        print "no action taken for this downward migration.";
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}