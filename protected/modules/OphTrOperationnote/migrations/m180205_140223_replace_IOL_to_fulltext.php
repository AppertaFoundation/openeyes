<?php

class m180205_140223_replace_IOL_to_fulltext extends CDbMigration
{

    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        //not using criteria here because this fix comes with yii 1.1.16: 3379 iterator bugfix #3453
        $dataProvider = new CActiveDataProvider('Procedure');
        $iterator = new CDataProviderIterator($dataProvider);

        if ($iterator->getTotalItemCount()) {
            foreach ($iterator as $i => $procedure) {
                $save = false;
                $data = [
                    'table' => 'proc',
                    'model' => 'Procedure',
                    'old_term' => $procedure->term,
                ];

                $words = explode(" ", $procedure->term);
                foreach ($words as $k => $word) {
                    if ($word === 'IOL') {
                        $words[$k] = 'Intraocular lens';

                        $procedure->term = implode(" ", $words);
                        $data['new_term'] = $procedure->term;

                        $save = true;
                    }
                }

                if ($save && $procedure->save()) {
                    \Audit::add('Admin', 'update', "<pre>" . (print_r($data, true)) . "</pre>", '', ['model' => 'Procedure']);
                }
            }
        }
    }

    public function safeDown()
    {
        echo "m180205_140223_replace_IOL_to_fulltext does not support migration down.\n";
        return false;
    }

}
