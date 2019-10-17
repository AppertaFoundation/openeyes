<?php

class m190603_084711_add_ophtr_operationnote_attribute extends OEMigration
{
    public function up()
    {
        $trans = Yii::app()->db->beginTransaction();
        try {
            $this->createOETable('ophtroperationnote_attribute', array(
                'id' => 'pk',
                'name' => 'VARCHAR(40) NOT NULL',
                'label' => 'VARCHAR(255) NOT NULL',
                'display_order' => 'INT(11) NOT NULL',
                'proc_id' => 'INT(10) UNSIGNED NOT NULL',
                'is_multiselect' => 'TINYINT NOT NULL DEFAULT 1'
            ), true);

            $this->addForeignKey('fk_ophtroperationnote_att_proc', 'ophtroperationnote_attribute', 'proc_id', 'proc', 'id');

            $this->createOETable('ophtroperationnote_attribute_option', array(
                'id' => 'pk',
                'value' => 'VARCHAR(255) NOT NULL',
                'attribute_id' => 'INT(11) NOT NULL'
            ), true);

            $this->addForeignKey('fk_ophtroperationnote_ao_attr_id', 'ophtroperationnote_attribute_option', 'attribute_id', 'ophtroperationnote_attribute', 'id');
        } catch (Exception $e) {
            $trans->rollback();
            return false;
        }

        $trans->commit();
    }

    public function down()
    {
        $this->dropForeignKey('fk_ophtroperationnote_ao_attr_id', 'ophtroperationnote_attribute_option');
        $this->dropOETable('ophtroperationnote_attribute_option', true);
        $this->dropForeignKey('fk_ophtroperationnote_att_proc', 'ophtroperationnote_attribute');
        $this->dropOETable('ophtroperationnote_attribute', true);
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