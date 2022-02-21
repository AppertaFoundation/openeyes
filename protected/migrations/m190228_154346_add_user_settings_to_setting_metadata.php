<?php

class m190228_154346_add_user_settings_to_setting_metadata extends CDbMigration
{
    function getElementTypeId()
    {
        return $this->dbConnection->createCommand()
            ->select('id')
            ->from('element_type')
            ->where('class_name = :class_name', [':class_name' => 'Element_OphTrOperationnote_Cataract'])
            ->queryScalar();
    }

    function getFieldTypeId($field_type)
    {
        return $this->dbConnection->createCommand()
            ->select('id')
            ->from('setting_field_type')
            ->where('name = :name', [':name' => $field_type])
            ->queryScalar();
    }

    private $rotation_array = [
                'Superior' => 0,
                'Supero-nasal' => 0.7853981633974483,
                'Nasal' => 1.5707963267948966,
                'Infero-nasal' => 2.356194490192345,
                'Inferior' => 3.141592653589793,
                'Infero-temporal' => 3.9269908169872414,
                'Temporal' => 4.71238898038469,
                'Supero-temporal' => 5.497787143782138
            ];

    function metadataFields()
    {
        return [
            'Number of ports' => [
                'default_value' => 2, 'data' => serialize([1 => 'Single', 2 => 'Dual port']), 'field_type' => 'Dropdown list', 'display_order' => 7
            ],
            'Incision length' => [
                'default_value' => 2.8, 'data' => '', 'field_type' => 'Text Field', 'display_order' => 6
            ],
            'Incision centre position right eye' => [
                'default_value' => 180, 'data' => '', 'field_type' => 'Text Field', 'display_order' => 3
            ],
            'Incision centre position left eye' => [
                'default_value' => 0, 'data' => '', 'field_type' => 'Text Field', 'display_order' => 2
            ],
            'Surgeon position right eye' => [
                'default_value' => 'Temporal', 'data' => serialize($this->rotation_array), 'field_type' => 'Dropdown list', 'display_order' => 5
            ],
            'Surgeon position left eye' => [
                'default_value' => 'Nasal', 'data' => serialize($this->rotation_array), 'field_type' => 'Dropdown list', 'display_order' => 4
            ],
        ];
    }

    public function safeUp()
    {
        $element_type_id = $this->getElementTypeId();
        $field_types = [];
        foreach ($this->metadataFields() as $meta_field => $meta_field_data) {
            if (in_array($meta_field_data['field_type'], $field_types)) {
                $field_type_id = $field_types[$meta_field_data['field_type']];
            } else {
                $field_type_id = $this->getFieldTypeId($meta_field_data['field_type']);
                $field_types[$meta_field_data['field_type']] = $field_type_id;
            }

            $this->insert('setting_metadata', array(
                'element_type_id' => $element_type_id,
                'display_order' => $meta_field_data['display_order'],
                'field_type_id' => $field_type_id,
                'key' => str_replace(" ", "_", strtolower($meta_field)),
                'name' => $meta_field,
                'data' => $meta_field_data['data'],
                'default_value' => $meta_field_data['default_value']
            ));
        }
    }

    public function safeDown()
    {
        $element_type_id = $this->getElementTypeId();
        foreach ($this->metadataFields() as $meta_field => $meta_field_data) {
            $this->delete('setting_metadata', 'element_type_id = "'.$element_type_id.'" AND name = "'.$meta_field.'"');
        }
    }
}
