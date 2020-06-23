<?php

class m200605_110001_convert_MyISAM_to_InnoDB extends OEMigration
{
    public function safeUp()
    {
        // Find all MyISAM tables
        $listTables = Yii::app()->db->createCommand()
            ->select("table_name")
            ->from("information_schema.tables")
            ->where("engine LIKE 'MyISAM'")
            ->andWhere("table_type = 'BASE TABLE'")
            ->andWhere('table_schema = :oedb', array(':oedb' => (getenv('DATABASE_NAME') ?: 'openeyes')))
            ->queryAll();

        // Convert to InnoDB
        foreach ($listTables as $table) {
            echo "Converting " . $table['table_name'] . " to InnoDB Engine...";

            $this->execute("ALTER TABLE `" . $table['table_name'] . "` ENGINE = InnoDB");
        }
    }

    public function down()
    {
        echo "Down not supported";
    }
}
