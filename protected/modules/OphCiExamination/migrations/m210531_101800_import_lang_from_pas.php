<?php

class m210531_101800_import_lang_from_pas extends OEMigration
{
    public function safeUp()
    {
        if (false !== ($file = fopen(__DIR__."/data/m210531_101800_import_lang_from_pas/pas_lang_list.csv", "r"))) {
            //Increase name length so all language names will fit in
            $this->alterOEColumn('language', 'name', 'varchar(255) NOT NULL', true);

            $transaction = Yii::app()->db->getCurrentTransaction();
            if ($transaction !== null) {
                // Transaction already started outside
                $transaction = null;
            } else {
                // There is no outer transaction, creating a local one
                $transaction = Yii::app()->db->beginTransaction();
            }

            while ($data = fgetcsv($file, 1024, ",")) {
                $code = $data[0];
                $lang = $data[1];

                if ($record = Language::model()->findByAttributes(["pas_term" => $code])) {
                    continue;
                }

                if (!$record = Language::model()->findByAttributes(["name" => $lang])) {
                    $record = new Language();
                    $record->name = $lang;
                }

                echo $record->name . "\n";

                $record->pas_term = $code;
                if (!$record->save(true)) {
                    $transaction->rollback();
                    return false;
                }
            }

            if ($transaction !== null) {
                $transaction->commit();
            }

            return true;
        } else {
            echo "Unable to open input file".PHP_EOL;
            return false;
        }
    }

    public function safeDown()
    {
        echo "The m210531_101800_import_lang_from_pas migration does not support down.";
        return true;
    }
}
