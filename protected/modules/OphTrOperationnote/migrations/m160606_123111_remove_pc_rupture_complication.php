<?php

class m160606_123111_remove_pc_rupture_complication extends CDbMigration
{
    /**
     * @return bool|void
     * @throws CException
     */
    public function up()
    {
        $pcRupture = $this->dbConnection->createCommand('SELECT id FROM ophtroperationnote_cataract_complication WHERE name = "PC rupture"')
            ->queryScalar();
        $vitreousLoss = $this->dbConnection->createCommand('SELECT id FROM ophtroperationnote_cataract_complication WHERE name = "Vitreous loss"')
            ->queryScalar();
        $pcRuptureNoLoss = $this->dbConnection
            ->createCommand('SELECT id FROM ophtroperationnote_cataract_complication WHERE name = "PC rupture no vitreous loss"')
            ->queryScalar();
        $pcRuptureLoss = $this->dbConnection
            ->createCommand('SELECT id FROM ophtroperationnote_cataract_complication WHERE name = "PC rupture with vitreous loss"')
            ->queryScalar();

        foreach ($this->dbConnection
                     ->createCommand('SELECT cataract_id FROM ophtroperationnote_cataract_complication WHERE complication_id = :id')
                     ->bindValue(':id', $pcRupture)
                     ->queryAll() as $complication) {
            $lossCount = $this->dbConnection->createCommand()
                ->select('count(*)')
                ->from('ophtroperationnote_cataract_complication')
                ->where('cataract_id=:id1 and complication_id=:id2', array(':id1' => $complication['cataract_id'], ':id2' => $vitreousLoss))
                ->queryScalar();

            $this->update(
                'ophtroperationnote_cataract_complication',
                array('complication_id' => ($lossCount == 0) ? $pcRuptureNoLoss : $pcRuptureLoss),
                'id = :id',
                array(':id' => $complication)
            );
        }

        $this->delete(
            'ophtroperationnote_cataract_complication',
            'id = :id',
            array(':id' => $pcRupture)
        );
        //$pcRupture->delete();
    }

    public function down()
    {
        echo "m160606_123111_remove_pc_rupture_complication does not support migration down, as could be dangerous to restore previous history; however original data saved in ophtroperationnote_cataract_complication_version\n";
    }
}
