<?php

class m211025_043627_generate_pin extends OEMigration
{
    public function safeUp()
    {
        // get existing pins
        $pincodes = $this->dbConnection->createCommand()
            ->select('user_id, pincode')
            ->from('user_authentication')
            ->group('user_id')
            ->order('last_modified_date desc, last_successful_login_date desc')
            ->queryAll();

        $this->createOETable('user_pincode', [
            'id' => 'pk',
            'user_id' => 'int(10) unsigned',
            'pincode' => 'varchar(6) not null',
        ], true);
        $this->addForeignKey('user_pincode_user_fk', 'user_pincode', 'user_id', 'user', 'id');

        $existing_pincodes = array();

        foreach ($pincodes as $pincode) {
            if ($pincode['pincode']) {
                // if the user has pincode, migrate it regardless.
                $existing_pincodes[] = $pincode['pincode'];
            } else {
                // if the user has no pincde, generate a random one
                $temp_pincode = sprintf("%06d", mt_rand(0, 999999));
                // the random one needs to be unique, none sequential, none repeat or not same as secretary_pin
                while ($this->isSequentialNum($temp_pincode) || $this->isRepeatNum($temp_pincode) || in_array($temp_pincode, $existing_pincodes) || $temp_pincode === Yii::app()->params["secretary_pin"]) {
                    $temp_pincode = sprintf("%06d", mt_rand(0, 999999));
                }
                $pincode['pincode'] = $temp_pincode;
                $existing_pincodes[] = $temp_pincode;
            }
            $this->insert('user_pincode', $pincode);
        }

        $this->dropOEColumn('user_authentication', 'pincode', true);

        // get all the pins that are currently in use or used in the last 12 months
        $this->execute("
			CREATE OR REPLACE
			VIEW `v_unavailable_pincodes` AS
			SELECT pincode
			FROM user_pincode
			UNION
			SELECT pincode
			FROM user_pincode_version
			WHERE version_date > NOW() - INTERVAL 12 month;
		");
    }

    public function safeDown()
    {
        echo "m211017_235935_generate_pin does not support migration down.\n";
        return false;
    }

    private function isSequentialNum($num)
    {
        $count = 1;

        for ($i = 0; $i < strlen($num); $i++) {
            if ((substr($num, $i, 1) + 1) == substr($num, $i + 1, 1)) {
                $count++;
            }
        }

        return $count === strlen($num);
    }

    private function isRepeatNum($num)
    {
        return preg_match('/(\d)\1{5}/', $num);
    }
}
