<?php

class m170810_093446_update_workflows_for_deprecated_elements extends OEMigration
{
    protected static $deprecated_classes = array(
        'OEModule\OphCiExamination\models\Element_OphCiExamination_Allergy' => 'OEModule\OphCiExamination\models\Allergies',
        'OEModule\OphCiExamination\models\Element_OphCiExamination_Conclusion' => null,
        'OEModule\OphCiExamination\models\Element_OphCiExamination_HistoryRisk' => 'OEModule\OphCiExamination\models\HistoryRisks'
    );
    public function up()
    {
        foreach (static::$deprecated_classes as $deprecated => $replacement) {
            $deprecated_id = $this->getIdOfElementTypeByClassName($deprecated);
            if ($replacement !== null) {
                $replacement_id = $this->getIdOfElementTypeByClassName($replacement);
                $this->update('ophciexamination_element_set_item',
                    array('element_type_id' => $replacement_id),
                    'element_type_id = :deprecated_id', array(':deprecated_id' => $deprecated_id));
            } else {
                $this->delete('ophciexamination_element_set_item',
                    'element_type_id = :deprecated_id',
                    array(':deprecated_id' => $deprecated_id));
            }
        }
    }

    public function down()
    {
        /**
         * Essentially it's not possible to migrate down, but it's not very helpful to prevent the downward process when the up is
         * benign in nature (no action is taken unless deprecated elements are setup in workflows)
         */
        echo "WARNING: m170810_093446_update_workflows_for_deprecated_elements does not revert changes, but will still process down as a benign action.";
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