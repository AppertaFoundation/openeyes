<?php

class m191125_091623_create_event_icon_table extends CDbMigration
{

    public function up()
    {

        $icon_names = ['i-genCiDiamond', 'i-genCiDoc', 'i-genCiDocHole', 'i-genCiFour', 'i-genCiGrid',
            'i-genCiNut', 'i-genCiSquare', 'i-genCiSquareDot', 'i-genCiSquareSplit', 'i-genCiSquareWin',
            'i-genCiStack', 'i-genCiTarget', 'i-genCoDiamond', 'i-genCoDoc', 'i-genCoDocHole', 'i-genCoFour',
            'i-genCoGrid', 'i-genCoNut', 'i-genCoSquare', 'i-genCoSquareDot', 'i-genCoSquareSplit',
            'i-genCoSquareWin', 'i-genCoStack', 'i-genCoTarget', 'i-genImDiamond', 'i-genImDoc',
            'i-genImDocHole', 'i-genImFour', 'i-genImGrid', 'i-genImNut', 'i-genImSquare', 'i-genImSquareDot',
            'i-genImSquareSplit', 'i-genImSquareWin', 'i-genImStack', 'i-genImTarget', 'i-genMiDiamond',
            'i-genMiDoc', 'i-genMiDocHole', 'i-genMiFour', 'i-genMiGrid', 'i-genMiNut', 'i-genMiSquare', 'i-genMiSquareDot',
            'i-genMiSquareSplit', 'i-genMiSquareWin', 'i-genMiStack', 'i-genMiTarget', 'i-genNuDiamond', 'i-genNuDoc',
            'i-genNuDocHole', 'i-genNuFour', 'i-genNuGrid', 'i-genNuNut', 'i-genNuSquare', 'i-genNuSquareDot',
            'i-genNuSquareSplit', 'i-genNuSquareWin', 'i-genNuStack', 'i-genNuTarget'];

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

        $this->createTable('sub_type_event_icon', [
            'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'name' => 'varchar(64) not null',
            'display_order' => 'int unsigned not null'
        ]);

        foreach ($icon_names as $key => $event_icon) {
            $key = ($key + 1) * 10;
            $this->insert('sub_type_event_icon', ['name' => $event_icon, 'display_order' => $key]);
        }

        $this->addColumn('ophcodocument_sub_types', 'sub_type_event_icon_id', 'int(10) unsigned');
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
        $this->dropTable('sub_type_event_icon');
    }

}