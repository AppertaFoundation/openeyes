<?php

class m200220_033135_add_file_content_column_to_protected_file extends OEMigration
{
    /**
     * @return bool|void
     * @throws CException
     */
    public function safeUp()
    {
        $this->addOEColumn('protected_file', 'file_content', 'longblob', true);
        $this->addOEColumn('protected_file', 'thumbnail', 'longblob', true);

        $this->createOETable(
            'protected_file_thumbnail',
            array(
                'id' => 'pk',
                'file_id' => 'int(10) unsigned NOT NULL',
                'width' => 'int unsigned NOT NULL',
                'height' => 'int unsigned NOT NULL',
                'size' => 'bigint unsigned NOT NULL',
                'thumbnail_content' => 'longblob NOT NULL',
            ),
            true
        );

        $this->addForeignKey(
            'protected_file_thumbnail_file_id_fk',
            'protected_file_thumbnail',
            'file_id',
            'protected_file',
            'id',
            'CASCADE',
        );

        $all_files = $this->dbConnection->createCommand()
            ->select('id, uid')
            ->from('protected_file')
            ->query();
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
        $this->dropForeignKey(
            'protected_file_thumbnail_file_id_fk',
            'protected_file_thumbnail'
        );
        $this->dropOETable('protected_file_thumbnail', true);
        $this->dropOEColumn('protected_file', 'file_content', true);
        $this->dropOEColumn('protected_file', 'thumbnail', true);
    }
}
