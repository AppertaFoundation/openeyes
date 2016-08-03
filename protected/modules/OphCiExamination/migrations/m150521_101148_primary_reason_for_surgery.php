<?php

class m150521_101148_primary_reason_for_surgery extends OEMigration
{
    protected $reasons = array(
        'Cataract removal for visual improvement',
        'Cataract removal for improvement of fundus view',
        'Cataract/clear lens removal as part of planned PPV',
        'Clear lens extraction for glaucoma',
        'Cataract/clear lens extraction for anisometropia',
        'Cataract/clear lens extraction for refractive reasons',
        'Cataract/clear lens extraction for other reasons',
    );

    public function up()
    {
        $this->createOETable('ophciexamination_primary_reason_for_surgery', array(
            'id' => 'pk',
            'name' => 'varchar(255) not null',
            'active' => 'tinyint(1) not null default 0',
        ), true);

        foreach ($this->reasons as $reason) {
            $this->insert('ophciexamination_primary_reason_for_surgery', array('name' => $reason, 'active' => 1));
        }

        $this->createTable('et_ophciexamination_cataractsurgicalmanagement_surgery_reasons', array(
            'id' => 'pk',
            'cataractsurgicalmanagement_id' => 'int unsigned',
            'primary_reason_for_surgery_id' => 'int',
        ));

        $this->addForeignKey(
            'cataractsurgicalmanagement_id_fk',
            'et_ophciexamination_cataractsurgicalmanagement_surgery_reasons',
            'cataractsurgicalmanagement_id',
            'et_ophciexamination_cataractsurgicalmanagement',
            'id'
        );

        $this->addForeignKey(
            'primary_reason_for_surgery_fk',
            'et_ophciexamination_cataractsurgicalmanagement_surgery_reasons',
            'primary_reason_for_surgery_id',
            'ophciexamination_primary_reason_for_surgery',
            'id'
        );
    }

    public function down()
    {
        $this->dropOETable('ophciexamination_primary_reason_for_surgery', true);
        $this->dropTable('et_ophciexamination_cataractsurgicalmanagement_surgery_reasons');
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
