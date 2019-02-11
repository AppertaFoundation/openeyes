<?php

class m160606_123111_remove_pc_rupture_complication extends CDbMigration
{
    public function up()
    {
        $pcRupture = OphTrOperationnote_CataractComplications::model()->findByAttributes(array('name' => 'PC rupture'));
        $vitreousLoss = OphTrOperationnote_CataractComplications::model()->findByAttributes(array('name' => 'Vitreous loss'));
        $pcRuptureNoLoss = OphTrOperationnote_CataractComplications::model()->findByAttributes(array('name' => 'PC rupture no vitreous loss'));
        $pcRuptureLoss = OphTrOperationnote_CataractComplications::model()->findByAttributes(array('name' => 'PC rupture with vitreous loss'));

        foreach (OphTrOperationnote_CataractComplication::model()->findAll('complication_id = :id', array('id' => $pcRupture->id)) as $complication) {
            $lossCount = $this->dbConnection->createCommand()->select('count(*)')->from('ophtroperationnote_cataract_complication')->where('cataract_id=:id1 and complication_id=:id2', array(':id1' => $complication->cataract_id, ':id2' => $vitreousLoss->id))->queryScalar();

            $complication->complication_id = ($lossCount == 0) ? $pcRuptureNoLoss->id : $pcRuptureLoss->id;
            $complication->save();
        }

        $pcRupture->delete();
    }

    public function down()
    {
        echo "m160606_123111_remove_pc_rupture_complication does not support migration down, as could be dangerous to restore previous history; however original data saved in ophtroperationnote_cataract_complication_version\n";
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
