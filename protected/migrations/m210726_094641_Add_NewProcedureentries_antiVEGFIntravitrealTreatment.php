<?php

class m210726_094641_Add_NewProcedureentries_antiVEGFIntravitrealTreatment extends OEMigration
{
    private $benefits = array();
    private $complications = array();
    private $short_formats = array();
    private $procIDs = array();
    private $complicationsIDs = array();
    private $benefitsIDs = array();


    public function safeUp()
    {
        $benefits = array(
            'To improve or stabilise central vision/eyesight',
            'To improve/stabilise central vision'
        );
        $complications = array(
            'Endophthalmitis (1:1500 per injection)',
            'retinal detachment',
            'retinal tear',
            'vitreous haemorrhage',
            'cataract',
            'intraocular inflammation',
            'raised intraocular pressure',
            'red eye',
            'floaters',
            'headache',
            'hypertension',
            'stroke',
            'infection',
            'haemorrhage'
        );
        $short_formats = array(
            'Lucentis Intravit',
            'Eylea Intravit',
            'Beovu Intravit',
            'Avastin Intravit',
        );
        foreach ($benefits as $benefit) {
            $this->insert(
                'benefit',
                array('name' => $benefit),
            );
        }
        foreach ($complications as $complication) {
            $this->insert(
                'complication',
                array('name' => $complication),
            );
        }

        // inserting values in 'proc' table
        $this->insert(
            'proc',
            array(
                'term' => 'Course of LUCENTIS anti-VEGF Intravitreal Treatment by either nurse practitioner or doctor',
                'short_format' => 'Lucentis Intravit',
                'default_duration' => '10',
                'snomed_code' => '525991000000108',
                'snomed_term' => 'Injection of Ranibizumab into vitreous body (procedure)',
                'aliases' => '',
                'unbooked' => '1'
            ),
        );
        $this->insert(
            'proc',
            array(
                'term' => 'Course of EYLEA anti-VEGF Intravitreal Treatment by either nurse practitioner or doctor',
                'short_format' => 'Eylea Intravit',
                'default_duration' => '10',
                'snomed_code' => '1004045004',
                'snomed_term' => 'Intravitreal injection of anti-vascular endothelial growth factor (procedure)',
                'aliases' => '',
                'unbooked' => '1'
            ),
        );
        $this->insert(
            'proc',
            array(
                'term' => 'Course of BEOVU anti-VEGF Intravitreal Treatment by either nurse practitioner or doctor',
                'short_format' => 'Beovu Intravit',
                'default_duration' => '10',
                'snomed_code' => '1004045004',
                'snomed_term' => 'Intravitreal injection of anti-vascular endothelial growth factor (procedure)',
                'aliases' => '',
                'unbooked' => '1'
            ),
        );
        $this->insert(
            'proc',
            array(
                'term' => 'Course of AVASTIN anti-VEGF Intravitreal Treatment by either nurse practitioner or doctor',
                'short_format' => 'Avastin Intravit',
                'default_duration' => '10',
                'snomed_code' => '1004045004',
                'snomed_term' => 'Intravitreal injection of anti-vascular endothelial growth factor (procedure)',
                'aliases' => '',
                'unbooked' => '1'
            ),
        );

        // get ids of procedure, benefit ans complication
        foreach ($short_formats as $short_format) {
            $procID = Yii::app()->db->createCommand()
                ->select('id')
                ->from('proc')
                ->where('short_format=:short_format', array(':short_format' => $short_format))
                ->queryScalar();

            $procIDs[] = $procID;
        }
        foreach ($complications as $complication) {
            $complicationID = Yii::app()->db->createCommand()
                ->select('id')
                ->from('complication')
                ->where('name=:name', array(':name' => $complication))
                ->queryScalar();
            $complicationsIDs[] = $complicationID;
        }
        foreach ($benefits as $benefit) {
            $benefitID = Yii::app()->db->createCommand()
                ->select('id')
                ->from('benefit')
                ->where('name=:name', array(':name' => $benefit))
                ->queryScalar();
            $benefitsIDs[] = $benefitID;
        }
        // populate the multiarray for proc => complication
        for ($i = 0; $i < count($procIDs); $i++) {
            for ($j = 0; $j < count($complicationsIDs); $j++) {
                $procComplication[] = array($procIDs[$i] => $complicationsIDs[$j]);
            }
        }
        foreach ($procComplication as  $pcKey => $pcValue) {
            foreach ($pcValue as $key => $value) {
                $this->insert(
                    'procedure_complication',
                    array(
                        'proc_id' => $key,
                        'complication_id' => $value,
                    ),
                );
            }
        }
        // populate the multiarray for proc => benefit
        for ($i = 0; $i < count($procIDs); $i++) {
            for ($j = 0; $j < count($benefitsIDs); $j++) {
                $procBenifits[] = array($procIDs[$i] => $benefitsIDs[$j]);
            }
        }
        foreach ($procBenifits as  $pbKey => $pbValue) {
            foreach ($pbValue as $key => $value) {
                $this->insert(
                    'procedure_benefit',
                    array(
                        'proc_id' => $key,
                        'benefit_id' => $value,
                    ),
                );
            }
        }
    }
    public function safeDown()
    {
        echo "m210726_094641_Add_NewProcedureentries_antiVEGFIntravitrealTreatment does not support migration down.\n";
        return false;
    }
}
