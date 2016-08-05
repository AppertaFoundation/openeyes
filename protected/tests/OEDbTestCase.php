<?php

class OEDbTestCase extends CDbTestCase
{
    public $test_tables = array();

    protected static function createTestTable($table, $fields, $foreign_keys = null)
    {
        $fields['created_user_id'] = 'int(10) unsigned NOT NULL default 1';
        $fields['last_modified_user_id'] = 'int(10) unsigned NOT NULL default 1';
        $fields['created_date'] = "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'";
        $fields['last_modified_date'] = "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'";
        $fields[] = 'PRIMARY KEY (id)';

        if (Yii::app()->testdb->schema->getTable($table)) {
            echo "Warning: test table '$table' already exists!\n";

            return;
        }

        Yii::app()->testdb->createCommand(Yii::app()->testdb->schema->createTable($table, $fields, 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci'))->execute();

        if (!empty($foreign_keys)) {
            foreach ($foreign_keys as $key_name => $def) {
                Yii::app()->testdb->createCommand(Yii::app()->testdb->schema->addForeignKey($key_name, $table, $def[0], $def[1], $def[2]))->execute();
            }
        }

        Yii::app()->testdb->createCommand(Yii::app()->testdb->schema->addForeignKey($table.'_cui_fk', $table, 'created_user_id', 'user', 'id'))->execute();
        Yii::app()->testdb->createCommand(Yii::app()->testdb->schema->addForeignKey($table.'_lmui_fk', $table, 'last_modified_user_id', 'user', 'id'))->execute();
    }

    protected static function dropTable($table)
    {
        Yii::app()->testdb->createCommand(Yii::app()->testdb->schema->dropTable($table))->execute();
    }
}
