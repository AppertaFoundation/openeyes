<?php

class m150515_094332_connectToCataractElement extends CDbMigration
{
    protected function array_column($array, $column)
    {
        $return = array();
        foreach ($array as $key => $val) {
            if (isset($val[$column])) {
                $return[] = $val[$column];
            }
        }

        return $return;
    }

    public function up()
    {
        /*
        /* this migration is responsible for connecting both the cataract and biometry element together to operation note procedures
        */

        $findTable = Yii::app()->db->schema->getTable('ophtroperationnote_procedure_element');

        if (!$findTable) {
            echo '**WARNING** Cannot run migration, because OphTrOperationnote modules tables are not presented! Please install OphTrOperationnote module, and run this migration manually!';
        } else {
            // we connect the element with the operation note module
            $eventType = $this->dbConnection->createCommand()->select('*')->from('event_type')->where('class_name = :class_name',
                array(':class_name' => 'OphTrOperationnote'))->queryRow();

            // the biometry element data
            $biometryElementType = $this->dbConnection->createCommand()->select('*')->from('element_type')->where('event_type_id = :event_type_id and class_name = :class_name',
                array(
                    ':event_type_id' => $eventType['id'],
                    ':class_name' => 'Element_OphTrOperationnote_Biometry',
                ))->queryRow();

            // the cataract element data
            $cataractElementType = $this->dbConnection->createCommand()->select('*')->from('element_type')->where('event_type_id = :event_type_id and class_name = :class_name',
                array(
                    ':event_type_id' => $eventType['id'],
                    ':class_name' => 'Element_OphTrOperationnote_Cataract',
                ))->queryRow();

            // these are the procedures we currently know as cataract procedures
            $procedures = array('Aspiration of lens',
                                'Extracapsular cataract extraction',
                                'Extracapsular cataract extraction and insertion of IOL',
                                'Insertion of IOL',
                                'IOL exchange',
                                'IOL insertion - anterior chamber',
                                'IOL insertion - posterior chamber',
                                'IOL insertion - sutured',
                                'Other extraction of lens',
                                'Phakic IOL insertion',
                                'Phakoemulsification and IOL',
                                'Removal of IOL',
                                'Repositioning of IOL',
                                'Revision of IOL',
                                'Secondary lens implant',
                );
            // search the procedure IDs
            $proceduresData = $this->dbConnection->createCommand()->select('id')->from('proc')->where(array('in', 'term', $procedures))->queryAll();

            // search for procedures connected with the cataract element
            if ($cataractElementType) {
                $cataractElementProcedures = $this->dbConnection->createCommand()->select('procedure_id AS id')
                    ->from('ophtroperationnote_procedure_element')
                    ->where('element_type_id = :cataract_element_type_id',
                        array(':cataract_element_type_id' => $cataractElementType['id'])
                    )->queryAll();
                // we check if there are any new procedures
                //if( is_array($cataractElementProcedures) && is_array($proceduresData) ) {
                //	$difference = array_diff_assoc($cataractElementProcedures, $proceduresData);
                //}

                // if we found any new we add them to the base array
                //if (is_array($difference) && count($difference) > 0) {
                //	$proceduresData = array_merge($proceduresData, $difference);
                //}
            }
            // we search for current biometry element procedure relations
            $biometryElementProcedures = $this->dbConnection->createCommand()->select('procedure_id AS id')
                ->from('ophtroperationnote_procedure_element')
                ->where('element_type_id = :biometry_element_type_id',
                    array(':biometry_element_type_id' => $biometryElementType['id'])
                )->queryAll();

            foreach ($proceduresData as $procedure) {
                if ($cataractElementType) {
                    if (!in_array($procedure['id'], $this->array_column($cataractElementProcedures, 'id'))) {
                        $this->insert('ophtroperationnote_procedure_element', array('procedure_id' => $procedure['id'], 'element_type_id' => $cataractElementType['id']));
                    }
                }
                if (!in_array($procedure['id'], $this->array_column($biometryElementProcedures, 'id'))) {
                    $this->insert('ophtroperationnote_procedure_element', array('procedure_id' => $procedure['id'], 'element_type_id' => $biometryElementType['id']));
                }
            }
        }
    }

    public function down()
    {
        $currentTables = $this->dbConnection->getSchema()->getTableNames();

        if (!in_array('ophtroperationnote_procedure_element', $currentTables)) {
            echo '**WARNING** Cannot run migration, because OphTrOperationnote modules tables are not presented! Please install OphTrOperationnote module, and run this migration manually!';
        } else {
            // we connect the element with the operation note module
            $eventType = $this->dbConnection->createCommand()->select('*')->from('event_type')->where('class_name = :class_name',
                array(':class_name' => 'OphTrOperationnote'))->queryRow();

            // the biometry element data
            $biometryElementType = $this->dbConnection->createCommand()->select('*')->from('element_type')->where('event_type_id = :event_type_id and class_name = :class_name',
                array(
                    ':event_type_id' => $eventType['id'],
                    ':class_name' => 'Element_OphTrOperationnote_Biometry',
                ))->queryRow();

            // the cataract element data
            $cataractElementType = $this->dbConnection->createCommand()->select('*')->from('element_type')->where('event_type_id = :event_type_id and class_name = :class_name',
                array(
                    ':event_type_id' => $eventType['id'],
                    ':class_name' => 'Element_OphTrOperationnote_Cataract',
                ))->queryRow();

            $this->delete('ophtroperationnote_procedure_element', "element_type_id = {$biometryElementType['id']}");
            if ($cataractElementType) {
                $this->delete('ophtroperationnote_procedure_element', "element_type_id = {$cataractElementType['id']}");
            }
        }
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
