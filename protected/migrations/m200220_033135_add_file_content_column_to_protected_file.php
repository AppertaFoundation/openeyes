<?php

class m200220_033135_add_file_content_column_to_protected_file extends OEMigration
{
    /**
     * @return bool|void
     * @throws CException
     */
    public function safeUp()
    {
        $this->addOEColumn('protected_file', 'file_content', 'blob');
        $this->addOEColumn('protected_file', 'thumbnail_content', 'blob');

        $all_files = $this->dbConnection->createCommand()
            ->select('id, uid')
            ->from('protected_file')
            ->queryAll();
        foreach ($all_files as $file) {
            // Save the contents of the files on the application server to the database.
            $path = Yii::app()->basePath.'/files/'. $file['uid'][0] .'/'. $file['uid'][1] .'/'. $file['uid'][2] . '/' . $file['uid'];
            if (file_exists($path)) {
                $file_contents = file_get_contents($path);
                $this->update(
                    'protected_file',
                    array(
                        'file_content' => $file_contents,
                    ),
                    'id = :id',
                    array(':id' => $file['id'])
                );
            }
        }
    }

    public function safeDown()
    {
        $this->dropOEColumn('protected_file', 'file_content');
    }
}