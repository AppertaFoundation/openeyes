<?php

class m131021_133201_fully_sided_laser_management extends CDbMigration
{
    public function up()
    {
        $this->addColumn('et_ophciexamination_lasermanagement', 'left_laser_status_id', 'int(10) unsigned');
        $this->addColumn('et_ophciexamination_lasermanagement', 'right_laser_status_id', 'int(10) unsigned');
        $this->addColumn('et_ophciexamination_lasermanagement', 'left_laser_deferralreason_id', 'int(10) unsigned');
        $this->addColumn('et_ophciexamination_lasermanagement', 'right_laser_deferralreason_id', 'int(10) unsigned');
        $this->addColumn('et_ophciexamination_lasermanagement', 'left_laser_deferralreason_other', 'text');
        $this->addColumn('et_ophciexamination_lasermanagement', 'right_laser_deferralreason_other', 'text');

        $this->addForeignKey('et_ophciexamination_lasermanagement_l_laser_fk', 'et_ophciexamination_lasermanagement', 'left_laser_status_id', 'ophciexamination_management_status', 'id');
        $this->addForeignKey('et_ophciexamination_lasermanagement_r_laser_fk', 'et_ophciexamination_lasermanagement', 'right_laser_status_id', 'ophciexamination_management_status', 'id');
        $this->addForeignKey('et_ophciexamination_lasermanagement_l_ldeferral_fk', 'et_ophciexamination_lasermanagement', 'left_laser_deferralreason_id', 'ophciexamination_management_deferralreason', 'id');
        $this->addForeignKey('et_ophciexamination_lasermanagement_r_ldeferral_fk', 'et_ophciexamination_lasermanagement', 'right_laser_deferralreason_id', 'ophciexamination_management_deferralreason', 'id');

        // LEFT EYE MIGRATE
        foreach ($this->dbConnection->createCommand()->select('id, laser_status_id, laser_deferralreason_id, laser_deferralreason_other')
                     ->from('et_ophciexamination_lasermanagement')
                     ->where('eye_id = :eid', array(':eid' => 1))
                     ->order('id desc')->queryAll() as $lm) {
            $this->update(
                'et_ophciexamination_lasermanagement',
                array(
                    'left_laser_status_id' => $lm['laser_status_id'],
                    'left_laser_deferralreason_id' => $lm['laser_deferralreason_id'],
                    'left_laser_deferralreason_other' => $lm['laser_deferralreason_other'], ),
                'id = :id',
                array(':id' => $lm['id'])
            );
        }
        //RIGHT EYE MIGRATE
        foreach ($this->dbConnection->createCommand()->select('id, laser_status_id, laser_deferralreason_id, laser_deferralreason_other')
                     ->from('et_ophciexamination_lasermanagement')
                     ->where('eye_id = :eid', array(':eid' => 2))
                     ->order('id desc')->queryAll() as $lm) {
            $this->update(
                'et_ophciexamination_lasermanagement',
                array(
                'right_laser_status_id' => $lm['laser_status_id'],
                'right_laser_deferralreason_id' => $lm['laser_deferralreason_id'],
                'right_laser_deferralreason_other' => $lm['laser_deferralreason_other'], ),
                'id = :id',
                array(':id' => $lm['id'])
            );
        }
        // BOTH EYE MIGRATE
        foreach ($this->dbConnection->createCommand()->select('id, laser_status_id, laser_deferralreason_id, laser_deferralreason_other')
                     ->from('et_ophciexamination_lasermanagement')
                     ->where('eye_id = :eid', array(':eid' => 3))
                     ->order('id desc')->queryAll() as $lm) {
            $this->update(
                'et_ophciexamination_lasermanagement',
                array(
                'left_laser_status_id' => $lm['laser_status_id'],
                'left_laser_deferralreason_id' => $lm['laser_deferralreason_id'],
                'left_laser_deferralreason_other' => $lm['laser_deferralreason_other'],
                'right_laser_status_id' => $lm['laser_status_id'],
                'right_laser_deferralreason_id' => $lm['laser_deferralreason_id'],
                'right_laser_deferralreason_other' => $lm['laser_deferralreason_other'], ),
                'id = :id',
                array(':id' => $lm['id'])
            );
        }

        $this->dropForeignKey('et_ophciexamination_lasermanagement_laser_fk', 'et_ophciexamination_lasermanagement');
        $this->dropColumn('et_ophciexamination_lasermanagement', 'laser_status_id');
        $this->dropForeignKey('et_ophciexamination_lasermanagement_ldeferral_fk', 'et_ophciexamination_lasermanagement');
        $this->dropColumn('et_ophciexamination_lasermanagement', 'laser_deferralreason_id');
        $this->dropColumn('et_ophciexamination_lasermanagement', 'laser_deferralreason_other');
    }

    public function down()
    {
        $this->addColumn('et_ophciexamination_lasermanagement', 'laser_deferralreason_other', 'text');
        $this->addColumn('et_ophciexamination_lasermanagement', 'laser_deferralreason_id', 'int(10) unsigned');
        $this->addColumn('et_ophciexamination_lasermanagement', 'laser_status_id', 'int(10) unsigned');

        $this->addForeignKey('et_ophciexamination_lasermanagement_ldeferral_fk', 'et_ophciexamination_lasermanagement', 'laser_deferralreason_id', 'ophciexamination_management_deferralreason', 'id');
        $this->addForeignKey('et_ophciexamination_lasermanagement_laser_fk', 'et_ophciexamination_lasermanagement', 'laser_status_id', 'ophciexamination_management_status', 'id');

        //BOTH EYE MIGRATE DOWN (favour left eye values)
        foreach ($this->dbConnection->createCommand()->select('id, left_laser_status_id, left_laser_deferralreason_id, left_laser_deferralreason_other')
                     ->from('et_ophciexamination_lasermanagement')
                     ->where('eye_id = ?', array(3))
                     ->order('id desc')->queryAll() as $lm) {
            $this->update(
                'et_ophciexamination_lasermanagement',
                array(
                'laser_status_id' => $lm['left_laser_status_id'],
                'laser_deferralreason_id' => $lm['left_laser_deferralreason_id'],
                'laser_deferralreason_other' => $lm['left_laser_deferralreason_other'], ),
                'id = :id',
                array(':id' => $lm['id'])
            );
        }

        //RIGHT EYE MIGRATE DOWN
        foreach ($this->dbConnection->createCommand()->select('id, right_laser_status_id, right_laser_deferralreason_id, right_laser_deferralreason_other')
                     ->from('et_ophciexamination_lasermanagement')
                     ->where('eye_id = ?', array(2))
                     ->order('id desc')->queryAll() as $lm) {
            $this->update(
                'et_ophciexamination_lasermanagement',
                array(
                'laser_status_id' => $lm['right_laser_status_id'],
                'laser_deferralreason_id' => $lm['right_laser_deferralreason_id'],
                'laser_deferralreason_other' => $lm['right_laser_deferralreason_other'], ),
                'id = :id',
                array(':id' => $lm['id'])
            );
        }
        // LEFT EYE MIGRATE DOWN
        foreach ($this->dbConnection->createCommand()->select('id, left_laser_status_id, left_laser_deferralreason_id, left_laser_deferralreason_other')
                     ->from('et_ophciexamination_lasermanagement')
                     ->where('eye_id = ?', array(1))
                     ->order('id desc')->queryAll() as $lm) {
            $this->update(
                'et_ophciexamination_lasermanagement',
                array(
                'laser_status_id' => $lm['left_laser_status_id'],
                'laser_deferralreason_id' => $lm['left_laser_deferralreason_id'],
                'laser_deferralreason_other' => $lm['left_laser_deferralreason_other'], ),
                'id = :id',
                array(':id' => $lm['id'])
            );
        }

        $this->dropColumn('et_ophciexamination_lasermanagement', 'right_laser_deferralreason_other');
        $this->dropColumn('et_ophciexamination_lasermanagement', 'left_laser_deferralreason_other');
        $this->dropForeignKey('et_ophciexamination_lasermanagement_r_ldeferral_fk', 'et_ophciexamination_lasermanagement');
        $this->dropColumn('et_ophciexamination_lasermanagement', 'right_laser_deferralreason_id');
        $this->dropForeignKey('et_ophciexamination_lasermanagement_l_ldeferral_fk', 'et_ophciexamination_lasermanagement');
        $this->dropColumn('et_ophciexamination_lasermanagement', 'left_laser_deferralreason_id');
        $this->dropForeignKey('et_ophciexamination_lasermanagement_r_laser_fk', 'et_ophciexamination_lasermanagement');
        $this->dropColumn('et_ophciexamination_lasermanagement', 'right_laser_status_id');
        $this->dropForeignKey('et_ophciexamination_lasermanagement_l_laser_fk', 'et_ophciexamination_lasermanagement');
        $this->dropColumn('et_ophciexamination_lasermanagement', 'left_laser_status_id');
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
