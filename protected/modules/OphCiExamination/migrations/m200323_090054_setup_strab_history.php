<?php

use OEModule\OphCiExamination\models\Element_OphCiExamination_History;

class m200323_090054_setup_strab_history extends OEMigration
{
    protected $created_attributes = ['referral_sources' => 'Referral Source(s)'];

    protected $history_options = [
        'referral_sources' => [
            'options' => [
                'vision screening',
                'health visitor',
                'gp',
                'optometrist',
                'paediatrician'
            ],
            'multiselect' => true
        ],
        'history' => [
            'options' => [
                'reduced vision',
                'strabismus',
                'diplopia',
                'asthenopia',
                'ptosis',
                'field defect'
            ]
        ],
        'eye' => [
            'options' => ['both eyes', 'left eye', 'right eye']
        ],
        'duration' => [
            'options' => '*'
        ]
    ];

    protected $element_type_id_for_history;
    protected $subspecialty_id_for_strabismus;
    protected $subspecialty_id_for_paediatrics;

    public function safeUp()
    {
        $this->initialise();

        $this->createOETable('ophciexamination_attribute_option_exclude', [
            'id' => 'pk',
            'option_id' => 'int(10) unsigned NOT NULL',
            'subspecialty_id' => 'int(10) unsigned NOT NULL'
        ], true);

        // Ensure we have all the attributes defined
        foreach ($this->created_attributes as $name => $label) {
            $existing_attribute = $this->dbConnection->createCommand()->select('*')
                ->from('ophciexamination_attribute')
                ->where('name = :name', [':name' => $name])
                ->queryRow();
            if ($existing_attribute) {
                throw new Exception('cannot create already existing examination attribute ' . $name);
            }

            $this->insert('ophciexamination_attribute', ['name' => $name, 'label' => $label]);
            $attribute_id = $this->dbConnection->getLastInsertID();
            $this->insert(
                'ophciexamination_attribute_element',
                ['attribute_id' => $attribute_id, 'element_type_id' => $this->element_type_id_for_history]
            );
        }

        $relevant_attribute_element_ids = [];

        foreach ($this->history_options as $attribute_name => $attribute_details) {
            $attribute_id = $this->getAttributeIdForName($attribute_name);

            if (array_key_exists('multiselect', $attribute_details)) {
                $this->update('ophciexamination_attribute', ['is_multiselect' => $attribute_details['multiselect']]);
            }

            $attribute_element_id = $this->getAttributeElementId($attribute_id, $this->element_type_id_for_history);

            $this->setUpOptionsForAttribute(
                $attribute_element_id,
                [$this->subspecialty_id_for_strabismus, $this->subspecialty_id_for_paediatrics],
                $attribute_details['options']
            );

            $relevant_attribute_element_ids[] = $attribute_element_id;
        }

        $this->excludeAllOptionsForElementAttributeIds(
            $this->getOtherAttributeElementIds($relevant_attribute_element_ids, $this->element_type_id_for_history),
            [$this->subspecialty_id_for_strabismus, $this->subspecialty_id_for_paediatrics]
        );

        return true;
    }

    public function safeDown()
    {
        $this->initialise();
        $all_subspecialty_ids = [$this->subspecialty_id_for_strabismus, $this->subspecialty_id_for_paediatrics];
        $option_ids_to_delete = [];

        // work out what options to delete
        foreach ($this->history_options as $attribute_name => $attribute_details) {
            if (!is_array($attribute_details['options'])) {
                continue;
            }
            if (in_array($attribute_name, array_keys($this->created_attributes))) {
                // will be deleted later
                continue;
            }
            $attribute_id = $this->getAttributeIdForName($attribute_name);
            $attribute_element_id = $this->getAttributeElementId($attribute_id, $this->element_type_id_for_history);

            foreach ($attribute_details['options'] as $attr_option) {
                $option_id = $this->getOptionIdForValue($attribute_element_id, $attr_option);
                if ($option_id) {
                    if ($this->optionIsExcludedForOtherSubspecialties($option_id, $all_subspecialty_ids)) {
                        $option_ids_to_delete[] = $option_id;
                    }
                }
            }
        }

        $this->dropOETable('ophciexamination_attribute_option_exclude', true);

        foreach ($option_ids_to_delete as $option_id) {
            $this->delete(
                'ophciexamination_attribute_option',
                'id = ?',
                [$option_id]
            );
        }

        foreach ($this->created_attributes as $name => $label) {
            $params = [':attribute_id' => $this->getAttributeIdForName($name)];
            $this->delete('ophciexamination_attribute_option', 'attribute_element_id = :attribute_id', $params);
            $this->delete('ophciexamination_attribute_element', 'attribute_id = :attribute_id', $params);
            $this->delete('ophciexamination_attribute', 'id = :attribute_id', $params);
        }

        return true;
    }

    protected function initialise()
    {
        $this->element_type_id_for_history = $this->getIdOfElementTypeByClassName(Element_OphCiExamination_History::class);
        $this->subspecialty_id_for_strabismus = $this->getIdOfSubspecialtyByName('Strabismus');
        $this->subspecialty_id_for_paediatrics = $this->getIdOfSubspecialtyByName('Paediatrics');

        if (!$this->element_type_id_for_history || !$this->subspecialty_id_for_strabismus || !$this->subspecialty_id_for_paediatrics) {
            throw new Exception('cannot migrate with missing referential data');
        }
    }

    protected function getAttributeIdForName($name)
    {
        $attribute_id = $this->dbConnection->createCommand()->select('id')
            ->from('ophciexamination_attribute')
            ->where('name = :name', [':name' => $name])
            ->queryScalar();

        if (!$attribute_id) {
            throw new Exception('missing required examination attribute ' . $name);
        }

        return $attribute_id;
    }

    protected function getAttributeElementId($attribute_id, $element_type_id)
    {
        $attribute_element_id = $this->dbConnection->createCommand()->select('id')
            ->from('ophciexamination_attribute_element')
            ->where('attribute_id = :attribute_id AND element_type_id = :element_type_id', [
                ':attribute_id' => $attribute_id,
                ':element_type_id' => $element_type_id
            ])
            ->queryScalar();

        if (!$attribute_element_id) {
            throw new Exception('missing required examination attribute element relationship');
        }

        return $attribute_element_id;
    }

    protected function setUpOptionsForAttribute($attribute_element_id, $subspecialty_ids, $options)
    {
        if (!is_array($options) && $options === '*') {
            // nothing to do as we want all the options to be visible as they currently are
            return;
        }

        if (!is_array($subspecialty_ids)) {
            $subspecialty_ids = [$subspecialty_ids];
        }

        $display_order = $this->dbConnection->createCommand()
            ->select('MAX(display_order)')
            ->from('ophciexamination_attribute_option')
            ->where('attribute_element_id = :id', [':id' => $attribute_element_id])
            ->queryScalar();

        $keep_option_ids = [];
        $inserted_option_ids = [];

        // create and/or track existing options for the given attribute
        foreach ($options as $attr_option) {
            $option_id = $this->getOptionIdForValue($attribute_element_id, $attr_option);
            if ($option_id) {
                $keep_option_ids[] = $option_id;
            } else {
                $this->insert('ophciexamination_attribute_option', [
                    'attribute_element_id' => $attribute_element_id,
                    'value' => $attr_option,
                    'delimiter' => '',
                    'display_order' => ++$display_order
                ]);
                $inserted_option_ids[] = $this->dbConnection->getLastInsertID();
            }
        }

        // mark the new options as needing to be excluded for other subspecialties
        $this->excludeOptionsForOtherSubspecialtyIds($inserted_option_ids, $subspecialty_ids);
        // any other options in this column should be excluded for our subspecialties
        $this->excludeOtherOptionsForSubspecialtyIds(
            $attribute_element_id,
            array_merge($keep_option_ids, $inserted_option_ids),
            $subspecialty_ids
        );
    }

    protected function excludeOptionsForOtherSubspecialtyIds($option_ids, $subspecialty_ids)
    {
        $other_subspecialty_ids = $this->getOtherSubspecialtyIds($subspecialty_ids);
        foreach ($other_subspecialty_ids as $other_subspecialty_id) {
            foreach ($option_ids as $option_id) {
                $this->excludeOptionForSubspecialtyId($option_id, $other_subspecialty_id);
            }
        }
    }

    protected function excludeOtherOptionsForSubspecialtyIds($attribute_element_id, $option_ids, $subspecialty_ids)
    {
        $other_option_ids = $this->getOtherOptionIds($attribute_element_id, $option_ids);

        foreach ($subspecialty_ids as $subspecialty_id) {
            foreach ($other_option_ids as $other_option_id) {
                $this->excludeOptionForSubspecialtyId($other_option_id, $subspecialty_id);
            }
        }
    }

    protected function excludeAllOptionsForElementAttributeIds($attribute_element_ids, $subspecialty_ids)
    {
        if (!is_array($subspecialty_ids)) {
            $subspecialty_ids = [$subspecialty_ids];
        }

        foreach ($attribute_element_ids as $attribute_element_id) {
            foreach ($this->getOptionIdsForAttributeElement($attribute_element_id) as $option_id) {
                foreach ($subspecialty_ids as $subspecialty_id) {
                    $this->excludeOptionForSubspecialtyId($option_id, $subspecialty_id);
                }
            }
        }
    }

    private function getOptionIdForValue($attribute_element_id, $value)
    {
        return $this->dbConnection->createCommand()
            ->select('id')
            ->from('ophciexamination_attribute_option')
            ->where('attribute_element_id = ? AND value = ?', [$attribute_element_id, $value])
            ->queryScalar();
    }

    private function getOtherSubspecialtyIds($subspecialty_ids)
    {
        return array_map(
            function ($result) {
                return $result['id'];
            },
            $this->dbConnection->createCommand()
                ->select('id')
                ->from('subspecialty')
                ->where(['not in', 'id', $subspecialty_ids])
                ->queryAll()
        );
    }

    private function getOtherOptionIds($attribute_element_id, $option_ids)
    {
        return array_map(
            function ($result) {
                return $result['id'];
            },
            $this->dbConnection->createCommand()
                ->select('id')
                ->from('ophciexamination_attribute_option')
                ->where('attribute_element_id = ?', [$attribute_element_id])
                ->andWhere(['not in', 'id', $option_ids])
                ->queryAll()
        );
    }

    private function getOtherAttributeElementIds($attribute_element_ids, $element_type_id)
    {
        return array_map(
            function ($result) {
                return $result['id'];
            },
            $this->dbConnection->createCommand()
                ->select('id')
                ->from('ophciexamination_attribute_element')
                ->where('element_type_id = :id', [':id' => $element_type_id])
                ->where(['not in', 'id', $attribute_element_ids])
                ->queryAll()
        );
    }

    private function getOptionIdsForAttributeElement($attribute_element_id)
    {
        return array_map(
            function ($result) {
                return $result['id'];
            },
            $this->dbConnection->createCommand()
                ->select('id')
                ->from('ophciexamination_attribute_option')
                ->where('attribute_element_id = ?', [$attribute_element_id])
                ->queryAll()
        );
    }

    private function excludeOptionForSubspecialtyId($option_id, $subspecialty_id)
    {
        $this->insert('ophciexamination_attribute_option_exclude', [
            'option_id' => $option_id,
            'subspecialty_id' => $subspecialty_id
        ]);
    }

    private function optionIsExcludedForOtherSubspecialties($option_id, $subspecialty_ids)
    {
        if (!is_array($subspecialty_ids)) {
            $subspecialty_ids = [$subspecialty_ids];
        }

        return $this->dbConnection->createCommand()
                ->select('count(*)')
                ->from('ophciexamination_attribute_option_exclude')
                ->where("option_id = $option_id and subspecialty_id not in (" . implode(",", $subspecialty_ids) . ")")
                ->queryScalar() > 0;
    }
}
