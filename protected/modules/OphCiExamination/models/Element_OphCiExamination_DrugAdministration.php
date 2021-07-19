<?php

namespace OEModule\OphCiExamination\models;
class Element_OphCiExamination_DrugAdministration extends \Element_DrugAdministration
{
    protected $auto_update_relations = true;
    public static $entry_class = Element_OphCiExamination_DrugAdministration_record::class;

    public function defaultScope()
    {
        $alias = $this->getTableAlias(true, false);
        $local_scope = array(
            'condition'=>"{$alias}.type='{$this::getType()}'",
        );
        return array_merge($local_scope, parent::defaultScope());
    }
    public function getEntryRelations()
    {
        $rules = array(
            'entries' => array(
                self::HAS_MANY,
                Element_OphCiExamination_DrugAdministration_record::class,
                array('id' => 'event_id'),
                'through' => 'event',
                'on' => "usage_type = '" . Element_OphCiExamination_DrugAdministration_record::getUsageType() . "' AND usage_subtype = '" . Element_OphCiExamination_DrugAdministration_record::getUsageSubtype() . "' ",
                'order' => 'entries.start_date DESC, entries.end_date DESC, entries.last_modified_date'
            ),
            'visible_entries' => array(
                self::HAS_MANY,
                Element_OphCiExamination_DrugAdministration_record::class,
                array('id' => 'event_id'),
                'through' => 'event',
                'on' => "hidden = 0 AND usage_type = '" . Element_OphCiExamination_DrugAdministration_record::getUsageType() . "' AND usage_subtype = '" . Element_OphCiExamination_DrugAdministration_record::getUsageSubtype() . "' ",
                'order' => 'visible_entries.start_date DESC, visible_entries.end_date DESC, visible_entries.last_modified_date'
            ),
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
        );
        return array_merge($rules, $this->getAssignmentRelations());
    }

    public static function getType()
    {
        return 'exam';
    }
}
