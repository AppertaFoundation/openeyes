<?php

class m191218_121522_add_sub_type_event_icons_to_document_event extends CDbMigration
{
    public function up()
    {
        $sub_types = [
            'General' => 'i-genCiDocHole',
            'Biometry Report' => 'i-genImTarget',
            'Referral Letter' => 'i-genCoNut',
            'OCT' => 'i-genImSquare',
            'Electrocardiogram' => 'i-genCiDocHole',
            'Photograph' => 'i-genImSquareDot',
            'Consent Form' => 'i-genCoSquareWin',
            'Visual Field Report' => 'i-genImDiamond',
            'Lids Photo' => 'i-genCiDocHole',
            'Orbit Photo' => 'i-genCiDocHole',
            'Video' => 'i-genCoFour',
            'Refraction' => 'i-genCiFour',
            'Retcam' => 'i-genCiDocHole',
            'Toric IOL Calculation' => 'i-genImSquareSplit',
            'Ultrasound' => 'i-genImStack',
        ];

        $this->addColumn('ophcodocument_sub_types', 'sub_type_event_icon_id', 'int(11)');
        $this->addForeignKey('document_sub_type_event_icon_id_fk', 'ophcodocument_sub_types', 'sub_type_event_icon_id', 'sub_type_event_icon', 'id');

        $this->addColumn('ophcodocument_sub_types_version', 'sub_type_event_icon_id', 'int(10) unsigned');

        foreach ($sub_types as $sub_type => $icon) {  //set default values for document sub types
            $event_icon = SubTypeEventIcon::model()->find('name = ?', [$icon])->id;
            $this->update('ophcodocument_sub_types', ['sub_type_event_icon_id' => $event_icon], 'name="'.$sub_type .'"');
        }
    }

    public function down()
    {
        $this->dropForeignKey('document_sub_type_event_icon_id_fk', 'ophcodocument_sub_types');
        $this->dropColumn('ophcodocument_sub_types', 'sub_type_event_icon_id');
        $this->dropColumn('ophcodocument_sub_types_version', 'sub_type_event_icon_id');
    }

}