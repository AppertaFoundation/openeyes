<?php

class m190730_084706_upgrade_pupil_element extends OEMigration
{
    public function safeUp()
    {
        // Tables creation
        $this->createOETable(
            'et_ophciexamination_pupillary_abnormalities',
            array(
                'id' => 'pk',
                'event_id' => 'int(10) unsigned',
                'no_pupillaryabnormalities_date_left' => 'datetime',
                'no_pupillaryabnormalities_date_right' => 'datetime',
                'eye_id' => 'int(10) unsigned NOT NULL DEFAULT 3'
            ),
            true
        );
        $this->addForeignKey('et_ophciexamination_pupillary_abnormalities_ei_fk', 'et_ophciexamination_pupillary_abnormalities', 'event_id', 'event', 'id');
        $this->addForeignKey('et_ophciexamination_pupillary_abnormalities_eye_fk', 'et_ophciexamination_pupillary_abnormalities', 'eye_id', 'eye', 'id');

        $this->createOETable(
            'ophciexamination_pupillary_abnormality_entry',
            array(
                'id' => 'pk',
                'element_id' => 'int(11)',
                'abnormality_id' => 'int(10) unsigned',
                'has_abnormality' => 'tinyint(1) not null',
                'comments' => 'varchar(255) COLLATE utf8_bin',
                'eye_id' => 'int(10) unsigned NOT NULL'
            ),
            true
        );
        $this->addForeignKey('et_ophciexamination_pupillaryabnormality_entry_ei_fk', 'ophciexamination_pupillary_abnormality_entry', 'element_id', 'et_ophciexamination_pupillary_abnormalities', 'id');
        $this->addForeignKey('et_ophciexamination_pupillaryabnormality__entry_ab_fk', 'ophciexamination_pupillary_abnormality_entry', 'abnormality_id', 'ophciexamination_pupillaryabnormalities_abnormality', 'id');
        $this->addForeignKey('et_ophciexamination_pupillaryabnormality_entry_eye_fk', 'ophciexamination_pupillary_abnormality_entry', 'eye_id', 'eye', 'id');

        $this->createOETable(
            'ophciexamination_pupillary_abnormality_set',
            array(
                'id' => 'pk',
                'name' => 'varchar(255) COLLATE utf8_bin',
                'firm_id' => 'int(10) unsigned',
                'subspecialty_id' => 'int(10) unsigned',
            ),
            true
        );
        $this->addForeignKey('ophciexamination_pupillary_abnormality_set_firm', 'ophciexamination_pupillary_abnormality_set', 'firm_id', 'firm', 'id');
        $this->addForeignKey('ophciexamination_pupillary_abnormality_set_subspecialty', 'ophciexamination_pupillary_abnormality_set', 'subspecialty_id', 'subspecialty', 'id');

        $this->createOETable(
            'ophciexamination_pupillary_abnormality_set_entry',
            array(
                'id' => 'pk',
                'ophciexamination_abnormality_id' => 'int(10) unsigned',
                'gender' => 'varchar(1) COLLATE utf8_bin',
                'age_min' => 'int(3) unsigned',
                'age_max' => 'int(3) unsigned',
                'set_id' => 'int(11)'
            ),
            true
        );
        $this->addForeignKey('ophciexamination_pupillary_abnormality_set_entry_ab_fk', 'ophciexamination_pupillary_abnormality_set_entry', 'ophciexamination_abnormality_id', 'ophciexamination_pupillaryabnormalities_abnormality', 'id');
        $this->addForeignKey('ophciexamination_pupillary_abnormality_set_entry_set_fk', 'ophciexamination_pupillary_abnormality_set_entry', 'set_id', 'ophciexamination_pupillary_abnormality_set', 'id');

        $this->createElementType('OphCiExamination', 'Pupils', [
            'class_name' => 'OEModule\OphCiExamination\models\PupillaryAbnormalities',
            'group_name' => 'Visual Function',
            'display_order' => '130',
        ]);


        // Data migration
        $data_provider = new CActiveDataProvider('OEModule\OphCiExamination\models\Element_OphCiExamination_PupillaryAbnormalities');
        $iterator = new CDataProviderIterator($data_provider);

        foreach ($iterator as $item) {
            foreach (['left', 'right'] as $side) {
                // ‘Normal’ + RAPD No = “Confirm patient has no pupilary abnormalities”
                $item_data['no_pupillaryabnormalities_date_' . $side] = ($item->{$side . '_abnormality_id'} === '1' && $item->{$side . '_rapd'} === '2') ? $item->last_modified_date : null;
            }

            $this->insert('et_ophciexamination_pupillary_abnormalities', [
                'event_id' => $item->event_id,
                'no_pupillaryabnormalities_date_left' => $item_data['no_pupillaryabnormalities_date_left'],
                'no_pupillaryabnormalities_date_right' => $item_data['no_pupillaryabnormalities_date_right'],
                'eye_id' => $item->eye_id
            ]);

            $id = \Yii::app()->db->getLastInsertID();

            foreach (['left' => 1, 'right' => 2] as $side => $eye_id) {
                if ($item_data['no_pupillaryabnormalities_date_' . $side] === null) {
                    if ($item->{$side . '_abnormality_id'} === '1' || $item->{$side . '_abnormality_id'} === null) {
                        if ($item->{$side . '_rapd'} === '1') {
                            $this->insert('ophciexamination_pupillary_abnormality_entry', [
                                'element_id' => $id,
                                'abnormality_id' => 2,
                                'has_abnormality' => 1,
                                'comments' => $item->{$side . '_comments'},
                                'eye_id' => $eye_id
                            ]);
                        }
                    } else {
                        if ($item->{$side . '_rapd'} === '1') {
                            $this->insert('ophciexamination_pupillary_abnormality_entry', [
                                'element_id' => $id,
                                'abnormality_id' => 2,
                                'has_abnormality' => 1,
                                'comments' => $item->{$side . '_comments'},
                                'eye_id' => $eye_id
                            ]);
                            $this->insert('ophciexamination_pupillary_abnormality_entry', [
                                'element_id' => $id,
                                'abnormality_id' => $item->{$side . '_abnormality_id'},
                                'has_abnormality' => 1,
                                'eye_id' => $eye_id
                            ]);
                        } else {
                            $this->insert('ophciexamination_pupillary_abnormality_entry', [
                                'element_id' => $id,
                                'abnormality_id' => $item->{$side . '_abnormality_id'},
                                'has_abnormality' => 1,
                                'comments' => $item->{$side . '_comments'},
                                'eye_id' => $eye_id
                            ]);
                        }
                    }
                }
            }
        }

        //update elements for pupillary abnormalities list
        $this->update('ophciexamination_pupillaryabnormalities_abnormality', array('active' => 1), "id = '2'"); // enable 'RAPD' option
        $this->update('ophciexamination_pupillaryabnormalities_abnormality', array('active' => 0), "id = '1'"); // disable 'Normal' option
    }

    public function safeDown()
    {
        $this->dropForeignKey('et_ophciexamination_pupillaryabnormality_entry_ei_fk', 'ophciexamination_pupillary_abnormality_entry');
        $this->dropForeignKey('et_ophciexamination_pupillaryabnormality__entry_ab_fk', 'ophciexamination_pupillary_abnormality_entry');
        $this->dropForeignKey('et_ophciexamination_pupillaryabnormality_entry_eye_fk', 'ophciexamination_pupillary_abnormality_entry');

        $this->dropForeignKey('ophciexamination_pupillary_abnormality_set_entry_ab_fk', 'ophciexamination_pupillary_abnormality_set_entry');
        $this->dropForeignKey('ophciexamination_pupillary_abnormality_set_entry_set_fk', 'ophciexamination_pupillary_abnormality_set_entry');

        $this->dropForeignKey('ophciexamination_pupillary_abnormality_set_firm', 'ophciexamination_pupillary_abnormality_set');
        $this->dropForeignKey('ophciexamination_pupillary_abnormality_set_subspecialty', 'ophciexamination_pupillary_abnormality_set');

        $this->dropForeignKey('et_ophciexamination_pupillary_abnormalities_ei_fk', 'et_ophciexamination_pupillary_abnormalities');
        $this->dropForeignKey('et_ophciexamination_pupillary_abnormalities_eye_fk', 'et_ophciexamination_pupillary_abnormalities');

        $this->dropOETable('ophciexamination_pupillary_abnormality_entry', true);

        $this->dropOETable('et_ophciexamination_pupillary_abnormalities', true);

        $this->dropOETable('ophciexamination_pupillary_abnormality_set_entry', true);

        $this->dropOETable('ophciexamination_pupillary_abnormality_set', true);

        $this->delete('element_type', "class_name = :class_name", [':class_name' => 'OEModule\OphCiExamination\models\PupillaryAbnormalities']);
    }
}