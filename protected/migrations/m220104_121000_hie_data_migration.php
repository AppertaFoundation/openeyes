<?php

class m220104_121000_hie_data_migration extends OEMigration
{
    public function up()
    {
        $table = Yii::app()->db->schema->getTable('user', true);
        if (isset($table->columns['hie_access_level_id'])) {
            foreach ($this->dbConnection->createCommand()->select('*')->from('user')->queryAll() as $user) {
                if ($user['hie_access_level_id'] == 1) {
                    $itemname = 'HIE - View';
                } elseif ($user['hie_access_level_id'] == 2) {
                    $itemname = 'HIE - Admin';
                } elseif ($user['hie_access_level_id'] == 3) {
                    $itemname = 'HIE - Summary';
                } elseif ($user['hie_access_level_id'] == 4) {
                    $itemname = 'HIE - Extended';
                }
                $rows[] = [
                    'itemname' => $itemname,
                    'userid' => $user['id'],
                ];
            }
            $this->insertMultiple('authassignment', $rows);
            $this->dropForeignKey(
                'hie_access_level_fk',
                'user',
            );
            $this->dropOEColumn('user', 'hie_access_level_id', true);
        }
    }

    public function down()
    {
        $this->addOEColumn('user', 'hie_access_level_id', 'int(11) NOT NULL default 1', true);
    }
}
