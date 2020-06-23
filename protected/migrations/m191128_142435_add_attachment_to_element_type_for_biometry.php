<?php

class m191128_142435_add_attachment_to_element_type_for_biometry extends CDbMigration
{
    public function up()
    {
        $event_type_id = \Yii::app()->db->createCommand()->select('id')->from('event_type')->where('class_name=:class_name', [':class_name' => 'OphInBiometry'])->queryScalar();

        $this->insert('element_type', [
                'name' => 'Attachment',
                'class_name' => 'OEModule\OphGeneric\models\Attachment',
                'event_type_id' => $event_type_id,
                'display_order' => 1,
                'required' => 0
            ]);
    }

    public function down()
    {
        $event_type_id = \Yii::app()->db->createCommand()->select('id')->from('event_type')->where('class_name=:class_name', [':class_name' => 'OphInBiometry'])->queryScalar();
        $this->delete('element_type', 'class_name = ? AND event_type_id = ?', [ 'OEModule\OphGeneric\models\Attachment', $event_type_id]);
    }
}
