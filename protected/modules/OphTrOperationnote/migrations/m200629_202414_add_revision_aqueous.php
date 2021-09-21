<?php

class m200629_202414_add_revision_aqueous extends OEMigration
{
    public function safeUp()
    {
        $event_type_id = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name = :class_name', array(':class_name' => 'OphTrOperationnote'))->queryScalar();
        $this->createOETable('et_ophtroperationnote_revision_aqueous', array(
            'id' => 'pk',
            'event_id' => 'int(10) unsigned NOT NULL',
            'plate_pos_id' => 'int(10) unsigned DEFAULT NULL',
            'is_shunt_explanted' => 'tinyint(1) DEFAULT NULL',
            'final_tube_position_id' => 'int(10) unsigned DEFAULT NULL',
            'ripcord_suture_id' => 'int(10) unsigned DEFAULT NULL',
            'is_visco_in_ac' => 'tinyint(1) DEFAULT NULL',
            'is_flow_tested' => 'tinyint(1) DEFAULT NULL',
            'comments' => 'text NOT NULL',
        ), true);
        $this->insertOEElementType(array('Element_OphTrOperationnote_RevisionAqueousShunt' => array(
            'name' => 'Revision of aqueous shunt',
            'display_order' => 60,
            'parent_element_type_id' => 'Element_OphTrOperationnote_ProcedureList',
            'required' => 0,
            'default' => 0,
         )), $event_type_id);

        $this->addForeignKey(
            'et_ophtroperationnote_revisionaqueous_ev_fk',
            'et_ophtroperationnote_revision_aqueous',
            'event_id',
            'event',
            'id'
        );
// change default "Glaucoma Tube" element to "Revision of Aqueous Shun" element
        $glaucoma_tube_element_id = $this->dbConnection->createCommand()
            ->select('id')
            ->from('element_type')
            ->where('LOWER(name) = :name', array(':name' => 'glaucoma tube'))->queryScalar();
        $revision_element_id = $this->dbConnection->createCommand()
            ->select('id')
            ->from('element_type')
            ->where('LOWER(name) = :name', array(':name' => 'revision of aqueous shunt'))->queryScalar();
        $revision_proc_id = $this->dbConnection->createCommand()
            ->select('id')
            ->from('proc')
            ->where('LOWER(term) = :term', array(':term' => 'revision of aqueous shunt'))->queryScalar();

        $this->update(
            'ophtroperationnote_procedure_element',
            ['element_type_id' => $revision_element_id],
            'procedure_id = :procedure_id AND element_type_id = :e_type_id',
            [':procedure_id' => $revision_proc_id, ':e_type_id' => $glaucoma_tube_element_id]
        );
    }

    public function safeDown()
    {
        // change default "Revision of Aqueous Shun" element to "Glaucoma Tube" element
        $glaucoma_tube_element_id = $this->dbConnection->createCommand()
            ->select('id')
            ->from('element_type')
            ->where('name = :name', array(':name' => 'Glaucoma tube'))->queryScalar();
        $revision_element_id = $this->dbConnection->createCommand()
            ->select('id')
            ->from('element_type')
            ->where('name = :name', array(':name' => 'Revision of aqueous shunt'))->queryScalar();
        $revision_proc_id = $this->dbConnection->createCommand()
            ->select('id')
            ->from('proc')
            ->where('term = :term', array(':term' => 'Revision of aqueous shunt'))->queryScalar();

        $this->update(
            'ophtroperationnote_procedure_element',
            ['element_type_id' => $glaucoma_tube_element_id],
            'procedure_id = :procedure_id AND element_type_id = :e_type_id',
            [':procedure_id' => $revision_proc_id, ':e_type_id' => $revision_element_id]
        );
        $this->dropForeignKey('et_ophtroperationnote_revisionaqueous_ev_fk', 'et_ophtroperationnote_revision_aqueous');
        $this->delete('element_type', 'name = ?', ['Revision of aqueous shunt']);
        $this->dropOETable('et_ophtroperationnote_revision_aqueous', true);
    }
}
